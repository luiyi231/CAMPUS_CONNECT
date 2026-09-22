<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. TIPO_SOLICITUD
        Schema::create('tipos_solicitud', function (Blueprint $table) {
            $table->id('id_tipo');
            $table->string('nombre', 100);
            $table->timestamps();
        });

        // 2. ESTADO_SOLICITUD
        Schema::create('estados_solicitud', function (Blueprint $table) {
            $table->id('id_estado');
            $table->string('nombre', 50); // RECIBIDA, PENDIENTE, EN-PROCESO, COMPLETADA
            $table->timestamps();
        });

        // 3. USUARIO (si no existe tabla usuarios)
        if (! Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->id('id_usuario');
                $table->string('nombre', 100);
                $table->string('apellido', 100);
                $table->string('correo', 150)->unique();
                $table->string('contrasena');
                $table->string('rol', 50)->default('ESTUDIANTE'); // ADMINISTRADOR, RESPONSABLE, ESTUDIANTE
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 4. SOLICITUD
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id('id_solicitud');
            $table->unsignedBigInteger('id_estudiante');
            $table->unsignedBigInteger('id_responsable')->nullable();
            $table->unsignedBigInteger('id_tipo');
            $table->unsignedBigInteger('id_estado');
            $table->text('descripcion');
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();

            $table->foreign('id_estudiante')->references('id_usuario')->on('usuarios')->cascadeOnDelete();
            $table->foreign('id_responsable')->references('id_usuario')->on('usuarios')->nullOnDelete();
            $table->foreign('id_tipo')->references('id_tipo')->on('tipos_solicitud');
            $table->foreign('id_estado')->references('id_estado')->on('estados_solicitud');
        });

        // 5. EVIDENCIA
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id('id_evidencia');
            $table->unsignedBigInteger('id_solicitud');
            $table->string('archivo', 255);
            $table->timestamp('fecha_subida')->useCurrent();
            $table->timestamps();

            $table->foreign('id_solicitud')->references('id_solicitud')->on('solicitudes')->cascadeOnDelete();
        });

        // 6. HISTORIAL_ESTADO
        Schema::create('historial_estados', function (Blueprint $table) {
            $table->id('id_historial');
            $table->unsignedBigInteger('id_solicitud');
            $table->unsignedBigInteger('id_estado');
            $table->unsignedBigInteger('id_usuario');
            $table->timestamp('fecha_cambio')->useCurrent();
            $table->timestamps();

            $table->foreign('id_solicitud')->references('id_solicitud')->on('solicitudes')->cascadeOnDelete();
            $table->foreign('id_estado')->references('id_estado')->on('estados_solicitud');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_estados');
        Schema::dropIfExists('evidencias');
        Schema::dropIfExists('solicitudes');
        Schema::dropIfExists('estados_solicitud');
        Schema::dropIfExists('tipos_solicitud');
    }
};
