<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {

      "  DELIMITER $$

CREATE PROCEDURE FilterListings(
    IN p_status JSON,
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_minimum_price DOUBLE,
    IN p_maximum_price DOUBLE,
    IN p_market_segment INT,
    IN p_transaction_type INT,
    IN p_property_status INT,
    IN p_contract_type INT,
    IN p_state_id INT,
    IN p_province_id INT,
    IN p_city_id INT,
    IN p_zone_id INT,
    IN p_street_name VARCHAR(255),
    IN p_street_number VARCHAR(50),
    IN p_address VARCHAR(255),
    IN p_district VARCHAR(255),
    IN p_postal_code VARCHAR(50),
    IN p_property_type_id INT,
    IN p_market_status_id INT,
    IN p_category_id INT,
    IN p_min_rooms INT,
    IN p_max_rooms INT,
    IN p_min_bedrooms INT,
    IN p_max_bedrooms INT,
    IN p_min_toiletroom INT,
    IN p_max_toiletroom INT,
    IN p_min_bathrooms INT,
    IN p_max_bathrooms INT,
    IN p_min_parking INT,
    IN p_max_parking INT,
    IN p_min_total_sqm DECIMAL(10, 2),
    IN p_max_total_sqm DECIMAL(10, 2),
    IN p_min_year_built YEAR,
    IN p_max_year_built YEAR,
    IN p_min_floors INT,
    IN p_max_floors INT,
    IN p_characteristics JSON,
    IN p_polygon_wkt TEXT
)
BEGIN
    -- Temp table con IDs vendidos
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_sold_ids (
        listing_id INT PRIMARY KEY
    );

    INSERT IGNORE INTO temp_sold_ids (listing_id)
    SELECT DISTINCT t.listing_id
    FROM transactions t
    WHERE t.sold_date BETWEEN p_start_date AND p_end_date;
   -- SELECT COUNT(*) AS total_vendidos FROM temp_sold_ids;
    SELECT lis.*
    FROM listings lis
    INNER JOIN listing_prices lisprice ON lis.id = lisprice.listing_id
    INNER JOIN listings_information lisI ON lis.id = lisI.listing_id
    INNER JOIN locations loc ON lis.id = loc.listing_id
    INNER JOIN cities c ON loc.city_id = c.id
    INNER JOIN provinces p_loc ON c.province_id = p_loc.id
    LEFT JOIN feacture_listing lf2 ON lf2.listing_id = lis.id
    WHERE
        (p_status IS NULL OR JSON_LENGTH(p_status) = 0 OR JSON_CONTAINS(p_status, JSON_ARRAY(lis.status_listing_id)))

        AND (
            (p_start_date IS NULL AND p_end_date IS NULL)
            OR (
                lis.status_listing_id = 2 AND lis.date_of_listing BETWEEN p_start_date AND p_end_date
            )
            OR (
                lis.status_listing_id IN (3, 4, 5) AND lis.contract_end_date BETWEEN p_start_date AND p_end_date
            )
            OR (
                lis.status_listing_id = 6 AND lis.cancellation_date BETWEEN p_start_date AND p_end_date
            )
            OR (
                lis.status_listing_id IN (7, 8) AND EXISTS (
                    SELECT 1 FROM temp_sold_ids sold WHERE sold.listing_id = lis.id
                )
            )
        )

        -- Precios
        AND (p_minimum_price IS NULL OR lisprice.amount >= p_minimum_price)
        AND (p_maximum_price IS NULL OR lisprice.amount <= p_maximum_price)

        -- Filtros básicos
        AND (p_market_segment IS NULL OR lis.area_id = p_market_segment)
        AND (p_transaction_type IS NULL OR lis.transaction_type_id = p_transaction_type)
        AND (p_property_status IS NULL OR lisI.state_property_id = p_property_status)
        AND (p_contract_type IS NULL OR lis.contract_type_id = p_contract_type)
        AND (p_state_id IS NULL OR p_loc.state_id = p_state_id)
        AND (p_province_id IS NULL OR p_loc.id = p_province_id)
        AND (p_city_id IS NULL OR c.id = p_city_id)
        AND (p_zone_id IS NULL OR loc.zone_id = p_zone_id)

        -- Dirección
        AND (p_street_name IS NULL OR loc.second_address LIKE CONCAT('%', p_street_name, '%'))
        AND (p_address IS NULL OR loc.first_address LIKE CONCAT('%', p_address, '%'))
        AND (p_street_number IS NULL OR loc.number = p_street_number)
        AND (p_district IS NULL OR loc.district LIKE CONCAT('%', p_district, '%'))
        AND (p_postal_code IS NULL OR loc.zip_code = p_postal_code)

        -- Características
        AND (p_property_type_id IS NULL OR lisI.subtype_property_id = p_property_type_id)
        AND (p_market_status_id IS NULL OR lisI.market_status_id = p_market_status_id)
        AND (p_category_id IS NULL OR lisI.property_category_id = p_category_id)

        -- Rangos
        AND (p_min_rooms IS NULL OR lisI.total_number_rooms >= p_min_rooms)
        AND (p_max_rooms IS NULL OR lisI.total_number_rooms <= p_max_rooms)
        AND (p_min_bedrooms IS NULL OR lisI.number_bedrooms >= p_min_bedrooms)
        AND (p_max_bedrooms IS NULL OR lisI.number_bedrooms <= p_max_bedrooms)
        AND (p_min_bathrooms IS NULL OR lisI.number_bathrooms >= p_min_bathrooms)
        AND (p_max_bathrooms IS NULL OR lisI.number_bathrooms <= p_max_bathrooms)
        AND (p_min_toiletroom IS NULL OR lisI.number_toiletrooms >= p_min_toiletroom)
        AND (p_max_toiletroom IS NULL OR lisI.number_toiletrooms <= p_max_toiletroom)
        AND (p_min_parking IS NULL OR lisI.parking_slots >= p_min_parking)
        AND (p_max_parking IS NULL OR lisI.parking_slots <= p_max_parking)
        AND (p_min_total_sqm IS NULL OR lisI.total_area >= p_min_total_sqm)
        AND (p_max_total_sqm IS NULL OR lisI.total_area <= p_max_total_sqm)
        AND (p_min_year_built IS NULL OR lisI.year_construction >= p_min_year_built)
        AND (p_max_year_built IS NULL OR lisI.year_construction <= p_max_year_built)
        AND (p_min_floors IS NULL OR lisI.plant_numbers >= p_min_floors)
        AND (p_max_floors IS NULL OR lisI.plant_numbers <= p_max_floors)

        -- Geoespacial
        AND (
            p_polygon_wkt IS NULL
            OR ST_Contains(ST_GeomFromText(p_polygon_wkt), POINT(loc.longitude, loc.latitude))
        )

        -- Características extra (JSON)
        AND (p_characteristics IS NULL OR JSON_CONTAINS(p_characteristics, JSON_ARRAY(lf2.feature_id)))

    GROUP BY lis.id;

    DROP TEMPORARY TABLE IF EXISTS temp_sold_ids;
END $$

DELIMITER; ";

}

/**
 * Reverse the migrations.
 *
 * @return void
 */
  /**
 * Revierte las migraciones.
 */
    public function down(): void
    {

    }
};
