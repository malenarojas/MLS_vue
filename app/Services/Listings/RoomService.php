<?php

namespace App\Services\Listings;

use App\Models\Room;
use Illuminate\Support\Facades\Log;

class RoomService
{
    public function getAll(array $filter)
    {
        // Log::info('RoomService getAll', $filter);

        $listingId = $filter['listing_id'] ?? null;

        return Room::select(
            'rooms.id',
            'rooms.description',
            'rooms.size',
            'rooms.dimension_x',
            'rooms.dimension_y',
            'rooms.room_type_id'
        )
            ->when($listingId, function ($query) use ($listingId) {
                $query->join('listings_information', 'rooms.information_id', '=', 'listings_information.id')
                    ->where('listings_information.listing_id', $listingId);
            })
            ->get();
    }

    // public function getAll(array $data)
    // {
    //     Log::info('RoomService getAll', $data);
    //     $listingId = $data['listing_id'] ?? null;

    //     return Room::select(
    //         'id',
    //         'description',
    //         'size',
    //         'dimension_x',
    //         'dimension_y',
    //         'room_type_id'
    //     )->when($data['information_id'], function ($query) use ($data) {
    //         return $query->where('information_id', $data['information_id']);
    //     })->get();
    // }

    public function deleteById(int $id)
    {
        return Room::where('id', $id)->delete();
    }

    public function saveAll(array $rooms, int $informationId)
    {
        // Log::info('RoomService@saveAll', ['rooms' => $rooms, 'information_id' => $informationId]);
        if (empty($rooms)) return;

        $roomIds = array_filter(array_column($rooms, 'id')); // Filtrar IDs no nulos o no definidos

        // Recuperar registros existentes en un solo acceso
        $existingRooms = Room::whereIn('id', $roomIds)->pluck('id')->toArray();

        $roomsToUpdate = [];
        $roomsToInsert = [];

        // Dividir los datos en dos grupos: actualizar o insertar
        foreach ($rooms as $room) {
            $roomData = [
                'description' => $room['description'] ?? null,
                'size' => $room['size'] ?? null,
                'dimension_x' => $room['dimension_x'] ?? null,
                'dimension_y' => $room['dimension_y'] ?? null,
                'room_type_id' => $room['room_type_id'] ?? null,
                'information_id' => $informationId,
                'updated_at' => now(),
            ];

            if (!empty($room['id']) && in_array($room['id'], $existingRooms)) {
                $roomsToUpdate[] = array_merge(['id' => $room['id']], $roomData); // Actualizar con ID
            } else {
                $roomData['created_at'] = now();
                $roomsToInsert[] = $roomData;
            }
        }

        // Actualizar registros existentes
        if (!empty($roomsToUpdate)) {
            foreach ($roomsToUpdate as $room) {
                Log::info('RoomService@saveAll - Updating room', $room);
                Room::where('id', $room['id'])->update($room);
            }
        }

        // Insertar registros nuevos
        if (!empty($roomsToInsert)) {
            Log::info('RoomService@saveAll - Inserting rooms', $roomsToInsert);
            Room::insert($roomsToInsert); // Insertar en un solo acceso
        }
    }
}
