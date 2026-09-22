<?php

namespace Database\Seeders;

use App\Models\EstadoSolicitud;
use App\Models\Evidencia;
use App\Models\HistorialEstado;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CampusConnectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tipos de Solicitud (HU3)
        $tipos = [
            'Mantenimiento',
            'Soporte',
            'Infraestructura',
            'Otro',
        ];

        $tipoModels = [];
        foreach ($tipos as $tipo) {
            $tipoModels[$tipo] = TipoSolicitud::firstOrCreate(['nombre' => $tipo]);
        }

        // 2. Estados de Solicitud (HU4, HU5)
        $estados = [
            EstadoSolicitud::RECIBIDA,
            EstadoSolicitud::PENDIENTE,
            EstadoSolicitud::EN_PROCESO,
            EstadoSolicitud::COMPLETADA,
        ];

        $estadoModels = [];
        foreach ($estados as $estado) {
            $estadoModels[$estado] = EstadoSolicitud::firstOrCreate(['nombre' => $estado]);
        }

        // 3. Usuarios de demostración (Admin, Responsables, Estudiantes)
        $admin = User::firstOrCreate(
            ['correo' => 'admin@campusconnect.edu'],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Gutiérrez',
                'contrasena' => Hash::make('admin123'),
                'rol' => 'ADMINISTRADOR',
            ]
        );

        $responsable1 = User::firstOrCreate(
            ['correo' => 'mario.tecnico@campusconnect.edu'],
            [
                'nombre' => 'Mario',
                'apellido' => 'Flores',
                'contrasena' => Hash::make('password'),
                'rol' => 'RESPONSABLE',
            ]
        );

        $responsable2 = User::firstOrCreate(
            ['correo' => 'elena.infra@campusconnect.edu'],
            [
                'nombre' => 'Elena',
                'apellido' => 'Morales',
                'contrasena' => Hash::make('password'),
                'rol' => 'RESPONSABLE',
            ]
        );

        $estudiante1 = User::firstOrCreate(
            ['correo' => 'luigi.estudiante@campusconnect.edu'],
            [
                'nombre' => 'Luigi',
                'apellido' => 'Mendoza',
                'contrasena' => Hash::make('estudiante123'),
                'rol' => 'ESTUDIANTE',
            ]
        );

        $estudiante2 = User::firstOrCreate(
            ['correo' => 'valeria.estudiante@campusconnect.edu'],
            [
                'nombre' => 'Valeria',
                'apellido' => 'Gómez',
                'contrasena' => Hash::make('estudiante123'),
                'rol' => 'ESTUDIANTE',
            ]
        );

        $estudiante3 = User::firstOrCreate(
            ['correo' => 'andres.estudiante@campusconnect.edu'],
            [
                'nombre' => 'Andrés',
                'apellido' => 'Rojas',
                'contrasena' => Hash::make('estudiante123'),
                'rol' => 'ESTUDIANTE',
            ]
        );

        // 4. Solicitudes de muestra para poblar el Dashboard (HU1)
        // Solicitud 1: RECIBIDA (Sin responsable aún)
        $sol1 = Solicitud::create([
            'id_estudiante' => $estudiante1->id_usuario,
            'id_responsable' => null,
            'id_tipo' => $tipoModels['Infraestructura']->id_tipo,
            'id_estado' => $estadoModels[EstadoSolicitud::RECIBIDA]->id_estado,
            'descripcion' => 'El proyector interactivo del aula 204 no enciende ni detecta señal HDMI durante las clases matutinas.',
            'fecha_creacion' => now()->subHours(3),
            'fecha_actualizacion' => now()->subHours(3),
        ]);

        HistorialEstado::create([
            'id_solicitud' => $sol1->id_solicitud,
            'id_estado' => $estadoModels[EstadoSolicitud::RECIBIDA]->id_estado,
            'id_usuario' => $estudiante1->id_usuario,
            'fecha_cambio' => now()->subHours(3),
        ]);

        Evidencia::create([
            'id_solicitud' => $sol1->id_solicitud,
            'archivo' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=800&q=80',
            'fecha_subida' => now()->subHours(3),
        ]);

        // Solicitud 2: PENDIENTE (Con responsable asignado Mario)
        $sol2 = Solicitud::create([
            'id_estudiante' => $estudiante2->id_usuario,
            'id_responsable' => $responsable1->id_usuario,
            'id_tipo' => $tipoModels['Mantenimiento']->id_tipo,
            'id_estado' => $estadoModels[EstadoSolicitud::PENDIENTE]->id_estado,
            'descripcion' => 'Filtración de agua constante en el techo del laboratorio de cómputo 3, cerca a las tomas eléctricas.',
            'fecha_creacion' => now()->subDays(1),
            'fecha_actualizacion' => now()->subHours(12),
        ]);

        HistorialEstado::create([
            'id_solicitud' => $sol2->id_solicitud,
            'id_estado' => $estadoModels[EstadoSolicitud::RECIBIDA]->id_estado,
            'id_usuario' => $estudiante2->id_usuario,
            'fecha_cambio' => now()->subDays(1),
        ]);

        HistorialEstado::create([
            'id_solicitud' => $sol2->id_solicitud,
            'id_estado' => $estadoModels[EstadoSolicitud::PENDIENTE]->id_estado,
            'id_usuario' => $admin->id_usuario,
            'fecha_cambio' => now()->subHours(12),
        ]);

        Evidencia::create([
            'id_solicitud' => $sol2->id_solicitud,
            'archivo' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=800&q=80',
            'fecha_subida' => now()->subDays(1),
        ]);

        // Solicitud 3: EN-PROCESO (Responsable Elena)
        $sol3 = Solicitud::create([
            'id_estudiante' => $estudiante3->id_usuario,
            'id_responsable' => $responsable2->id_usuario,
            'id_tipo' => $tipoModels['Soporte']->id_tipo,
            'id_estado' => $estadoModels[EstadoSolicitud::EN_PROCESO]->id_estado,
            'descripcion' => 'Corte recurrente en la conexión WiFi Eduroam en los pisos 2 y 3 del edificio de Ingeniería.',
            'fecha_creacion' => now()->subDays(2),
            'fecha_actualizacion' => now()->subHours(5),
        ]);

        HistorialEstado::create([
            'id_solicitud' => $sol3->id_solicitud,
            'id_estado' => $estadoModels[EstadoSolicitud::RECIBIDA]->id_estado,
            'id_usuario' => $estudiante3->id_usuario,
            'fecha_cambio' => now()->subDays(2),
        ]);

        HistorialEstado::create([
            'id_solicitud' => $sol3->id_solicitud,
            'id_estado' => $estadoModels[EstadoSolicitud::EN_PROCESO]->id_estado,
            'id_usuario' => $admin->id_usuario,
            'fecha_cambio' => now()->subHours(5),
        ]);

        // Solicitud 4: COMPLETADA (Responsable Mario)
        $sol4 = Solicitud::create([
            'id_estudiante' => $estudiante1->id_usuario,
            'id_responsable' => $responsable1->id_usuario,
            'id_tipo' => $tipoModels['Mantenimiento']->id_tipo,
            'id_estado' => $estadoModels[EstadoSolicitud::COMPLETADA]->id_estado,
            'descripcion' => 'La cerradura del aula de estudio grupal 105 estaba trabada y no permitía el ingreso.',
            'fecha_creacion' => now()->subDays(4),
            'fecha_actualizacion' => now()->subDay(),
        ]);

        HistorialEstado::create([
            'id_solicitud' => $sol4->id_solicitud,
            'id_estado' => $estadoModels[EstadoSolicitud::RECIBIDA]->id_estado,
            'id_usuario' => $estudiante1->id_usuario,
            'fecha_cambio' => now()->subDays(4),
        ]);

        HistorialEstado::create([
            'id_solicitud' => $sol4->id_solicitud,
            'id_estado' => $estadoModels[EstadoSolicitud::EN_PROCESO]->id_estado,
            'id_usuario' => $admin->id_usuario,
            'fecha_cambio' => now()->subDays(2),
        ]);

        HistorialEstado::create([
            'id_solicitud' => $sol4->id_solicitud,
            'id_estado' => $estadoModels[EstadoSolicitud::COMPLETADA]->id_estado,
            'id_usuario' => $admin->id_usuario,
            'fecha_cambio' => now()->subDay(),
        ]);

        // Solicitud 5: RECIBIDA (Otro)
        $sol5 = Solicitud::create([
            'id_estudiante' => $estudiante2->id_usuario,
            'id_responsable' => null,
            'id_tipo' => $tipoModels['Otro']->id_tipo,
            'id_estado' => $estadoModels[EstadoSolicitud::RECIBIDA]->id_estado,
            'descripcion' => 'Falta de contenedores de reciclaje diferenciado en el área de cafetería central.',
            'fecha_creacion' => now()->subHours(1),
            'fecha_actualizacion' => now()->subHours(1),
        ]);

        HistorialEstado::create([
            'id_solicitud' => $sol5->id_solicitud,
            'id_estado' => $estadoModels[EstadoSolicitud::RECIBIDA]->id_estado,
            'id_usuario' => $estudiante2->id_usuario,
            'fecha_cambio' => now()->subHours(1),
        ]);
    }
}
