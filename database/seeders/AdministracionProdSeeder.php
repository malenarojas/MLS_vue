<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Page, Module, Section, Element, AccessRule};



class AdministracionProdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modulesadministracion = Module::updateOrCreate([
            'name' => 'Administracion RE/MAX'
        ], [
            'image' => '/intranet/BrokerDueno.jpg'
        ]);

        $pageadministracion = Page::updateOrCreate([
            'title' => 'Administracion RE/MAX',
            'module_id' => $modulesadministracion->id,
        ], [
            'image' => '/intranet/BrokerDueno.jpg',
        ]);
        $modulesadministracion->update(['default_page_id' => $pageadministracion->id]);

        $rolesPermitidosadministracion = ['Administrador', 'Broker'];

        foreach ($rolesPermitidosadministracion as $role) {
            AccessRule::updateOrCreate([
                'resource_type' => Module::class,
                'resource_id' => $modulesadministracion->id,
                'type' => 'role',
                'value' => $role,
                'access_type' => 'view',
            ]);
        }
        $pageProcedimientos2022 = Page::updateOrCreate([
            'title' => 'PROCEDIMIENTOS 2022',
            'module_id' => $modulesadministracion->id,
            'parent_id' => $pageadministracion->id, // ← esta es la página padre
        ], [
            'image' => '/intranet/BrokerDueno.jpg',
        ]);

        $pagecomunicados2022 = Page::updateOrCreate([
            'title' => 'COMUNICADOS 2022',
            'module_id' => $modulesadministracion->id,
            'parent_id' => $pageadministracion->id, // ← esta es la página padre
        ], [
            'image' => '/intranet/BrokerDueno.jpg',
        ]);
        $sectionComunicados2022 = Section::updateOrCreate([
            'page_id' => $pageProcedimientos2022->id,
            'title' => 'Comunicados 2022',
        ], [
            'order' => 1,
        ]);
        $elementosComunicados = [
            ['name' => '0012022 Comunicado - ABSTENCION DE COMERCIALIZACION.pdf', 'url' => 'https://drive.google.com/file/d/10qyUXqCNyhxsS6MSRy9MQLb5LWbiBZtf/view?usp=sharing'],
            ['name' => '0022022 Comunicado - EVENTOS REMAX.pdf', 'url' => 'https://drive.google.com/file/d/1BXuuSDH-z-bcNcpwpmVPB1auwv5ALzLW/view?usp=sharing'],
            ['name' => '0032022 Comunicado - ACCESOS A PLATAFORMAS REMAX.pdf', 'url' => 'https://drive.google.com/file/d/1Cmk_AteF5z1NBu58sakBuYhDwx6Sq-kc/view?usp=sharing'],
            ['name' => '0042022 Comunicado - CODIGO DE ETICA Y MANUAL DE AGENTE REMAX.pdf', 'url' => 'https://drive.google.com/file/d/1U1MzxEmVS7BUczVMlcKRs6bIOJPKrDZX/view?usp=sharing'],
            ['name' => '0052022 Comunicado - CONDOMINIOS TORRES ROBLE.pdf', 'url' => 'https://drive.google.com/file/d/19Z7oGlMGfci1wCIjUysKUyrqG7i-8uFJ/view?usp=sharing'],
            ['name' => '0062022 Comunicado - LINEA COMUNICACIONAL - TORRES ROBLE.pdf', 'url' => 'https://drive.google.com/file/d/1JfN8ZbIZ3jH3lHIh5Z5wpEIZ9n9AdDOC/view?usp=sharing'],
            ['name' => '0092022 Comunicado - REPORTE DE CIERRES AL SISTEMA.pdf', 'url' => 'https://drive.google.com/file/d/1IpYsLpQeavcArHRX5TrQO4lX8VOyWyD6/view?usp=sharing'],
            ['name' => '0102022 Comunicado - AGENTE DEL AÑO COCHABAMBA.pdf', 'url' => 'https://drive.google.com/file/d/1Xx2zRM8xWYYwmEiMCE3pQ0KjdFHwK-dW/view?usp=sharing'],
            ['name' => '0112022 Comunicado - DESCANSO STAFF REMAX BOLIVIA.pdf', 'url' => 'https://drive.google.com/file/d/1iXRRwI0wuZJQxXEwyp53rnYRUbTM8W49/view?usp=sharing'],
            ['name' => '0122022 Comunicado - TORRES ROBLES.pdf', 'url' => 'https://drive.google.com/file/d/1l71JtyOfhkBQG6hf8tsY_S_KG24E9vBL/view?usp=sharing'],
            ['name' => '0132022 Comunicado - LINEAMIENTO COMUNICACIONAL TORRES ROBLES.pdf', 'url' => 'https://drive.google.com/file/d/1R-tR12CDMmMlVNV27GM3rVLUrey0SYPr/view?usp=sharing'],
            ['name' => '0142022 Comunicado - CORRECTO USO DE LA MARCA REMAX.pdf', 'url' => 'https://drive.google.com/file/d/1WbIVrR-ZMeeXTqmHmuvPUnDDkvx2P9MC/view?usp=sharing'],
            ['name' => '0152022 Comunicado - CONTACTO AUTORIZADO REMAX BOLIVIA.pdf', 'url' => 'https://drive.google.com/file/d/1xmGV_ZWFqQT1ce2o5I_2TVCyy415J56S/view?usp=sharing'],
            ['name' => '0162022 Comunicado - REMAX BOULEVARD.pdf', 'url' => 'https://drive.google.com/file/d/1A7XO_4g0Un2qxSBLbwg9s5ZmZaRl0szA/view?usp=sharing'],
            ['name' => '0172022 Comunicado - DESCRIPCION DE PROPIEDADES SISTEMA ILIST.pdf', 'url' => 'https://drive.google.com/file/d/1Ri7r2GIfDxF-JdhlK2YcyMVB43SPCFex/view?usp=sharing'],
            ['name' => '0182022 Comunicado - SUSPENSION TEMPORAL SISTEMA QR.pdf', 'url' => 'https://drive.google.com/file/d/19OLEBcBF7GYSvbQLFX5jIxBI6D5dJ6kp/view?usp=sharing'],
            ['name' => '0192022 Comunicado - MANTENIMIENTO SISTEMA ILIST (2).pdf', 'url' => 'https://drive.google.com/file/d/13yN8NLx7VLIibEtXM1T5JtMgMTjStKFc/view?usp=sharing'],
            ['name' => '0202022 Comunicado - SUSPENSION DEL ACUERDO DE FRANQUICIA (1).pdf', 'url' => 'https://drive.google.com/file/d/1zRpTaNgSoXeyDDw4fWgKavoulGgdQDXR/view?usp=sharing'],
            ['name' => '0212022 Comunicado - VENCIMIENTO DE MEMBRESIAS DE AGENTES REMAX BOLIVIA (1).pdf', 'url' => 'https://drive.google.com/file/d/1e5RJDsmqsB8uHkDhCw47BStfb3n1DB4R/view?usp=sharing'],
            ['name' => '0222022 Comunicado - CARGADO DE CONTRATOS DE CAPTACION.pdf', 'url' => 'https://drive.google.com/file/d/13xfHswwq0chjlG_luERubViaYOthis4I/view?usp=sharing'],
            ['name' => '0232022 Comunicado - MANTENIMIENTO SISTEMA ILIST (II).pdf', 'url' => 'https://drive.google.com/file/d/1oLU2dTuSLFiRjxEaTi2uW8ZEZJqdaj0z/view?usp=sharing'],
            ['name' => '0242022 Comunicado - PARO CIVICO 25 DE JULIO DE 2022.pdf', 'url' => 'https://drive.google.com/file/d/12UDl0uXJ5WM-5l9DzvcJkEUqEpUh5Bm4/view?usp=sharing'],
            ['name' => '0252022 Comunicado - PARO CIVICO 05 DE AGOSTO DE 2022.pdf', 'url' => 'https://drive.google.com/file/d/1wiOlt7CRFnVtKzhMDIJKjJFgkARMXKFK/view?usp=sharing'],
            ['name' => '0262022 Comunicado - PREMIACIONES  Y RANKING TOP 100.pdf', 'url' => 'https://drive.google.com/file/d/1zNxYdJdMyctiBJshqn0EtWcUVJWHKi5t/view?usp=sharing'],
            ['name' => '0272022 Comunicado - LISTADO DE PROPIEDADES.pdf', 'url' => 'https://drive.google.com/file/d/1PAAYCzm707VFFonEkeyqsiBfq9rH3Fr_/view?usp=sharing'],
            ['name' => '0282022 Comunicado - CAMBIO SOCIETARIO REMAX PRO.pdf', 'url' => 'https://drive.google.com/file/d/1TIsrNrJnKzhPJ1EjkaZxEvEwq9-4QpwC/view?usp=sharing'],
            ['name' => '0292022 Comunicado - CAMBIO SOCIETARIO Y DE DENOMINACION REMAX INFINITY.pdf', 'url' => 'https://drive.google.com/file/d/1xMuQpz3gev6vOfeYne2Wx32irqAyH_EL/view?usp=sharing'],
            ['name' => '0302022 Comunicado - HABILITACION SISTEMA QR.pdf', 'url' => 'https://drive.google.com/file/d/1EwXpXY--vMm21zw7cUy3D-jre7C0mqwI/view?usp=sharing'],
            ['name' => '0312022 Comunicado - PUBLICACION DE INMUEBLES CAPTADOS POR REMAX.pdf', 'url' => 'https://drive.google.com/file/d/1LJmGUX9rLd3k7HKGiCnu02bTybVXjjYs/view?usp=sharing'],
            ['name' => '0322022 Comunicado - BLOQUEO DE NUMERO DESCONOCIDO.pdf', 'url' => 'https://drive.google.com/file/d/1G7pNfwo2V_e5mSkCuNE3mAHxPi1QEeP8/view?usp=sharing'],
            ['name' => '0332022 Comunicado - KIT DE BIENVENIDA AGENTES NUEVOS.pdf', 'url' => 'https://drive.google.com/file/d/1AsfrR0Un1FHgsfg1FQLn1SXm-wIuCx4o/view?usp=sharing'],
            ['name' => '0342022 Comunicado - ACTUALIZACION DE STATUS.pdf', 'url' => 'https://drive.google.com/file/d/1t1AU4FFB_TDgDiGrvV9SbEwDpHlk0fSm/view?usp=sharing'],
            ['name' => '0352022 Comunicado - NUEVO PROCEDIMIENTO DE RANGOS PARA AGENTES ASOCIADOS.pdf', 'url' => 'https://drive.google.com/file/d/1aH6bvi75bFpD0v0dOmoTF4DNQu31Nvhj/view?usp=sharing'],
            ['name' => '0362022 Comunicado - FACTURACION.pdf', 'url' => 'https://drive.google.com/file/d/1XGNLbYFs99i73dFEaiqPbyHEgH5gA07E/view?usp=sharing'],
            ['name' => '0372022 Comunicado - CERTIFICADO DE REGISTRO DE PROYECTOS Y CONTRATOS LIBRES DE CLÁUSULAS DE ABUSIVAS.pdf', 'url' => 'https://drive.google.com/file/d/1DB4kL9Hke0KUWJghBcqS5xjuvUDguYDP/view?usp=sharing'],
            ['name' => '0382022 Comunicado - RECIBOS REMAX.pdf', 'url' => 'https://drive.google.com/file/d/1fEjnabGMNCUkPEaL0GWp7hEcgSOoN8ht/view?usp=sharing'],
            ['name' => '0392022 Comunicado - ACLARACION – NUEVO PROCEDIMIENTO DE RANGOS PARA AGENTES ASOCIADOS (2).pdf', 'url' => 'https://drive.google.com/file/d/1hysgpea9KhTM-84bmHi1vFKu8Gp8lKNl/view?usp=sharing'],
            ['name' => '0392022 Comunicado - ACLARACION – NUEVO PROCEDIMIENTO DE RANGOS PARA AGENTES ASOCIADOS (2).pdf', 'url' => 'https://drive.google.com/file/d/1hysgpea9KhTM-84bmHi1vFKu8Gp8lKNl/view?usp=sharing'],
            ['name' => '0402022 Comunicado - PARO CIVICO INDEFINIDO.pdf', 'url' => 'https://drive.google.com/file/d/1TNPABlRZDfM-L9g0DfaYAYKgQ-Z_DX13/view?usp=share_link'],
            ['name' => '0412022 Comunicado - NUEVO HORARIO LABORAL STAFF REMAX BOLIVIA.pdf', 'url' => 'https://drive.google.com/file/d/1Ud22Oyb5U_lAZktd9yNWwKB_Lw-fMTLG/view?usp=sharing'],
            ['name' => '0422022 Comunicado - REMAX INMOBILIART.pdf', 'url' => 'https://drive.google.com/file/d/1-CzwXh-UAaTQBGBrJ8bPwWIKyuCJ59GE/view?usp=share_link'],
            ['name' => '0432022 Comunicado - ACLARATIVA PROCEDIMIENTO DE PREMIACIÓN REMAX - v6.pdf', 'url' => 'https://drive.google.com/file/d/1X396tlzd1xvc0U0eWfWrFF2SW59XRnO-/view?usp=share_link'],
            ['name' => '0442022 Comunicado - NUEVO PROCEDIMIENTO DE TRASPASO DE AGENTES REMAX.pdf', 'url' => 'https://drive.google.com/file/d/100pDi-Ukc1K5t3rMm-pD73eVkJFYfWpQ/view?usp=share_link'],
            ['name' => '0452022 Comunicado - VACACIONES COLECTIVAS STAFF REMAX BOLIVIA.pdf', 'url' => 'https://drive.google.com/file/d/1HHxnI-thnLKrVos7ZqMbaT07XeJ3_h-4/view?usp=share_link'],
            ['name' => '0462022 Comunicado - ADENDA - CLAUSULA DE NO COMPETENCIA FAMILIAR.pdf', 'url' => 'https://drive.google.com/file/d/1hZ_AVryYWJgV1aEASA2d49WdF_hlm1kD/view?usp=sharing'],
        ];

        $order = 1;
        foreach ($elementosComunicados as $item) {
            Element::updateOrCreate([
                'section_id' => $sectionComunicados2022->id,
                'type' => 'link',
                'order' => $order++,
                'content' => [
                    'name' => $item['name'],
                    'url' => $item['url'],
                ],
            ]);
        }
        $sectionprocedimientos2022 = Section::updateOrCreate([
            'page_id' => $pageProcedimientos2022->id,
            'title' => 'Procedimientos 2022',
        ], [
            'order' => 1,
        ]);

        $elementosProcedimientos2022 = [
            ['name' => 'MODIFICACIONES INSCRIPCION AGENTES NUEVOS- CIRB- RANGOS.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/MODIFICACIONES%20INSCRIPCION%20AGENTES%20NUEVOS-%20CIRB-%20RANGOS.pdf'],
            ['name' => 'PERFIL DEL ASISTENTE EN VENTAS DE UN AGENTE ASOCIADO.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/PERFIL%20DEL%20ASISTENTE%20EN%20VENTAS%20DE%20UN%20AGENTE%20ASOCIADO.pdf'],
            ['name' => 'PROCEDIMIENTO DE RANKING MENSUAL 2021.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/PROCEDIMIENTO%20DE%20RANKING%20MENSUAL%202021.pdf'],
            ['name' => 'RB 01 REGLAMENTO GENERAL REMAX.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2001%20REGLAMENTO%20GENERAL%20REMAX.pdf'],
            ['name' => 'RB 02 CODIGO DE PROCEDIMIENTO DE CONCILIACION.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2002%20CODIGO%20DE%20PROCEDIMIENTO%20DE%20CONCILIACION.pdf'],
            ['name' => 'RB 04 CODIGO DE ETICA.pdf', 'url' => 'https://drive.google.com/file/d/1589S3uRO3Qn_5ZJcRLhhlOjfOA9R7l3u/view?usp=drive_link'],
            ['name' => 'RB 07 PROCEDIMIENTO DE INSCRIPCIÓN DE AGENTES NUEVOS v07.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2007%20PROCEDIMIENTO%20DE%20INSCRIPCI%C3%93N%20DE%20AGENTES%20NUEVOS%20v07.pdf'],
            ['name' => 'RB 09 PROCEDIMIENTO DE REGULARIZACIÓN DE COMISIONES.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2009%20PROCEDIMIENTO%20DE%20REGULARIZACI%C3%93N%20DE%20COMISIONES.pdf'],
            ['name' => 'RB10 PROCEDIMIENTO DE RANGOS PARA AGENTES ASOCIADOS - v07.pdf', 'url' => 'https://drive.google.com/file/d/1ao-QkZGD00m-atRw2zlHzU03HjTbvbaV/view?usp=sharing'],
            ['name' => 'RB 12 PROCEDIMIENTO DE VENTA DE SOUVENIRS.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2012%20PROCEDIMIENTO%20DE%20VENTA%20DE%20SOUVENIRS.pdf'],
            ['name' => 'RB 13 PROCEDIMIENTO DE BAJA DE AGENTES.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2013%20PROCEDIMIENTO%20DE%20BAJA%20DE%20AGENTES.pdf'],
            ['name' => 'RB 14 PROCEDIMIENTO DE ACTOS VANDÁLICOS DE LETREROS v01.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2014%20PROCEDIMIENTO%20DE%20ACTOS%20VAND%C3%81LICOS%20DE%20LETREROS%20v01.pdf'],
            ['name' => 'RB 15 PROCEDIMIENTO DE COBRANZAS v01.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2015%20PROCEDIMIENTO%20DE%20COBRANZAS%20v01.pdf'],
            ['name' => 'RB 16 PROCEDIMIENTO REFERIDO DE AUDITORIAS.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2016%20PROCEDIMIENTO%20REFERIDO%20DE%20AUDITORIAS.pdf'],
            ['name' => 'RB 17 NORMATIVA DE COMISIONES DE ALQUILERES v03.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2017%20NORMATIVA%20DE%20COMISIONES%20DE%20ALQUILERES%20v03.pdf'],
            ['name' => 'RB 18 PROCEDIMIENTO DE PREMIACIÓN REMAX - v6.pdf', 'url' => 'https://drive.google.com/file/d/19shdchuy2PHO4rhRh9h6_mKsJhYJ15vi/view?usp=sharing'],
            ['name' => 'RB 19 PROCEDIMIENTO DE RENOVACIÓN DE MEMBRESÍAS v02.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2019%20PROCEDIMIENTO%20DE%20RENOVACI%C3%93N%20DE%20MEMBRES%C3%8DAS%20v02.pdf'],
            ['name' => 'RB 20 MATERIAL MKT PRÉSTAMO.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2020%20MATERIAL%20MKT%20PR%C3%89STAMO.pdf'],
            ['name' => 'RB 20 PROCEDIMIENTO PRÉSTAMO DE MATERIAL DE MKT.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2020%20PROCEDIMIENTO%20PR%C3%89STAMO%20DE%20MATERIAL%20DE%20MKT.pdf'],
            ['name' => 'RB 21 PROTOCOLO DE SEGURIDAD SANITARIA ANTE EL COVID-19.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/RB%2021%20PROTOCOLO%20DE%20SEGURIDAD%20SANITARIA%20ANTE%20EL%20COVID-19.pdf'],
            ['name' => 'REFERIMIENTOS Y COMPENSACIONES.pdf', 'url' => '/Sites/remaxbolivia/intranet/genericos/REFERIMIENTOS%20Y%20COMPENSACIONES.pdf']
        ];
        $order = 1;
        foreach ($elementosProcedimientos2022 as $item) {
            Element::updateOrCreate([
                'section_id' => $sectionprocedimientos2022->id,
                'type' => 'link',
                'order' => $order++,
                'content' => [
                    'name' => $item['name'],
                    'url' => $item['url'],
                ],
            ]);
        }



        $seccionesAdministracion = [
            'REUNIONES' => [
                ['type' => 'link', 'name' => 'CIERRE ANUAL DE BROKERS', 'url' => 'https://drive.google.com/file/d/1TXyNeJQYMwnwVBzK-CfXBgCU7d_OC4MO/view?usp=sharing'],
                ['type' => 'link', 'name' => 'REUNIÓN DE BROKERS 24 DE ENERO DE 2022', 'url' => 'https://drive.google.com/file/d/1dFc83hgGpv33UExFq55YIIul_AjguxTJ/view?usp=sharing'],
            ],
            'PROCEDIMIENTOS' => [
                ['type' => 'link', 'name' => 'FORMULARIO DE CIERRE', 'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSdCsklQNTCUqeFrtFRLouAD3E6oavzxkRoup_IzlLgRtJl6Xg/viewform'],
                ['type' => 'link', 'name' => 'RB 01 INSCRIPCIÓN DE AGENTES', 'url' => 'https://drive.google.com/file/d/1wu6S7prCL02oY20jALrvDupgHu9tVa4C/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'RB 24 PAGOS VIWENSI', 'url' => 'https://drive.google.com/file/d/1ZNUYbrSs2xTIQEZPbkONZIzkHV8FFtxa/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'RB 22 CAPTACIÓN Y COMERCIALIZACIÓN', 'url' => 'https://drive.google.com/file/d/1CiByWhksr873wlWVG9HloVc6ypl_3X72/view?usp=sharing'],
                ['type' => 'link', 'name' => 'RB 18 PREMIACIÓN ANUAL', 'url' => 'https://drive.google.com/file/d/19LEgpxElpKxhgn4B6NHTtkSNQNdJz3Mf/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'RB 13 BAJA DE AGENTES', 'url' => 'https://drive.google.com/file/d/11NDCoUfsFGQDj1McIS5dweAryYlS_Rw2/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'RB 11 TEAMS v04VF', 'url' => 'https://drive.google.com/file/d/1QBHscWLvWbMqIUatDApBzQaQ3vCqUkPX/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'RB 10 RANGOS AGENTES ASOCIADOS', 'url' => 'https://drive.google.com/file/d/1lKu1gMt3UOHx5vKZRpIbqBadT9p5CKZh/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'RB 03 MANUAL REMAX AGENTE', 'url' => 'https://drive.google.com/file/d/1Nx4QVxIfxIj3xCrQm57q0wqtdp6RTArO/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'PROCEDIMIENTOS 2023', 'url' => 'https://drive.google.com/drive/folders/1YzQHjcdkqDe5s0qD8DXlae3v8w7HZKdp?usp=sharing'],
                ['type' => 'link', 'name' => 'PROCEDIMIENTOS 2022', 'url' => 'http://intranet.remax-bolivia.prod.gryphtech.com/administracion/procedimientos.aspx'],
                ['type' => 'page', 'name' => 'PROCEDIMIENTOS 2022', 'page_id' => $pageProcedimientos2022->id],
            ],
            'MANUALES DE SISTEMA QR' => [
                ['type' => 'link', 'name' => 'QR - Altas y Bajas Agentes BO.pdf', 'url' => '/Sites/remaxbolivia/intranet/qr/QR%20-%20Altas%20y%20Bajas%20Agentes%20BO.pdf'],
                ['type' => 'link', 'name' => 'QR - Creación de Usuarios BO.pdf', 'url' => '/Sites/remaxbolivia/intranet/qr/QR%20-%20Creaci%C3%B3n%20de%20Usuarios%20BO.pdf'],
                ['type' => 'link', 'name' => 'QR - Rendiciones Cierres - BO.pdf', 'url' => '/Sites/remaxbolivia/intranet/qr/QR%20-%20Rendiciones%20Cierres%20-%20BO.pdf'],
            ],
            'COMUNICADOS' => [
                ['type' => 'page', 'name' => 'Comunicados 2022', 'page_id' => $pagecomunicados2022->id],
                ['type' => 'link', 'name' => 'Comunicados 2023', 'url' => 'https://drive.google.com/drive/folders/1m8ZjVs7NfrhbJfd98uxmRhvVQ5Xtb6W1?usp=drive_link'],
                ['type' => 'link', 'name' => 'Comunicados 2024', 'url' => 'https://drive.google.com/drive/folders/1cDwiXDB29zrkvF6V6PUTCwrToUQItKL9?usp=drive_link'],
                ['type' => 'link', 'name' => '010.25 Intentos de Estafa.pdf', 'url' => 'https://drive.google.com/file/d/1P1MwUMZ0yuDOrhKDM77kjYbo6fPuxD15/view?usp=drive_link'],
                ['type' => 'link', 'name' => '009.25 Rangos Premiación REMAX BOLIVIA VF.pdf', 'url' => 'https://drive.google.com/file/d/1neWb18ZeLtrAsy2hiFCGAfPjSeik2rac/view?usp=drive_link'],
                ['type' => 'link', 'name' => '006.25 Promoción Costo Cero Agentes.pdf', 'url' => 'https://drive.google.com/file/d/17ADFgak_TasDoj48hx-m3yUl7KsUjPa_/view?usp=drive_link'],
                ['type' => 'link', 'name' => '004.25 Alcance Territorial de Captaciones.pdf', 'url' => 'https://drive.google.com/file/d/1qOGiU-_8JqxW3vo4-UUH48LZnwgIk61k/view?usp=drive_link'],
                ['type' => 'link', 'name' => '002.2025 Distribución de Comisión.pdf', 'url' => 'https://drive.google.com/file/d/1dz7hbo1hTPcuBkLpgGihWnM1n2PEfDv3/view?usp=drive_link'],
            ],
        ];

        $order = 1;

        foreach ($seccionesAdministracion as $titulo => $elementos) {
            $section = Section::updateOrCreate([
                'page_id' => $pageadministracion->id,
                'title' => $titulo,
            ], [
                'order' => $order++,
            ]);

            $elementOrder = 1;

            foreach ($elementos as $item) {
                Element::updateOrCreate([
                    'section_id' => $section->id,
                    'type' => $item['type'],
                    'order' => $elementOrder++,
                    'content' => $item['type'] === 'page'
                    ? [
                        'name' => $item['name'],
                        'page_id' => $item['page_id'],
                    ]
                    : [
                        'name' => $item['name'],
                        'url' => $item['url'],
                    ],
                ]);
            }
        }
    }
}
