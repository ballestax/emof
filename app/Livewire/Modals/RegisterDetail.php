<?php

namespace App\Livewire\Modals;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PayRegister;
use App\Models\GlossRegister;

class RegisterDetail extends Component
{
    public $registerId;
    public $tableName;
    public $registerData = null;
    public bool $showModal = false;
    public $idCanasta = null;
    public $status = '';

    protected $listeners = ['showRegisterModal' => 'loadRegister'];

    public function loadRegister($registerId, $tableName, $idCanasta)
    {
        Log::info("RegisterDetail: Loading register ID {$registerId} from table {$tableName}");
        $this->registerId = $registerId;
        $this->tableName = $tableName;
        $this->showModal = true;
        $this->idCanasta = $idCanasta;
        $this->status = '';

        try {
            $this->registerData = DB::table($this->tableName)
                                    ->where('id', $this->registerId)
                                    ->first();

            if ($this->registerData) {
                // ---- Buscar Estado ----
                $this->findRegisterStatus();
                // ---- Fin Buscar Estado ----
            } else {
                Log::error("RegisterDetailModal: Register not found - ID {$this->registerId}, Table {$this->tableName}");
            }

        } catch (\Exception $e) {
            Log::error("RegisterDetail: Error loading register data - " . $e->getMessage(), [
                'registerId' => $this->registerId,
                'tableName' => $this->tableName
            ]);
             $this->registerData = null;
        }
    }

    protected function findRegisterStatus()
    {
        if (!$this->registerData || !$this->idCanasta) {
            $this->status = '';
            return;
        }

        // Prioridad: Pagado > Glosado
        try {
            // Verificar si está pagado
            $isPaid = PayRegister::where('id_register', $this->idCanasta)
                        ->where('consecutivo', $this->registerData->consecutivo)
                        ->where('tipo_documento', $this->registerData->tipo_documento)
                        ->where('numero_documento', $this->registerData->numero_documento)
                        ->exists(); // exists() es eficiente

            if ($isPaid) {
                $this->status = 'Pagado';
                Log::info("RegisterDetailModal: Status for Consecutivo {$this->registerData->consecutivo} in Canasta {$this->idCanasta} is PAGADO.");
                return; // Si está pagado, no necesitamos buscar en glosas
            }

            // Si no está pagado, verificar si está glosado
            $isGlossed = GlossRegister::where('id_register', $this->idCanasta)
                        ->where('consecutivo', $this->registerData->consecutivo)
                        ->where('tipo_documento', $this->registerData->tipo_documento)
                        ->where('numero_documento', $this->registerData->numero_documento)
                        ->exists();

            if ($isGlossed) {
                $this->status = 'Glosado';
                 Log::info("RegisterDetailModal: Status for Consecutivo {$this->registerData->consecutivo} in Canasta {$this->idCanasta} is GLOSADO.");
            } else {
                $this->status = 'Pendiente'; // O dejar vacío: ''
                Log::info("RegisterDetailModal: Status for Consecutivo {$this->registerData->consecutivo} in Canasta {$this->idCanasta} is PENDIENTE.");
            }

        } catch (\Exception $e) {
             Log::error("RegisterDetailModal: Error finding status - " . $e->getMessage(), [
                'registerId' => $this->registerId, 'tableName' => $this->tableName, 'idCanasta' => $this->idCanasta, 'consecutivo' => $this->registerData->consecutivo ?? null
            ]);
            $this->status = 'Error'; // Indicar error en estado
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->registerData = null;
        $this->status = '';
        $this->idCanasta = null;
        $this->dispatch('registerModalClosed');
    }

    public function render()
    {
        return view('livewire.modals.register-detail');
    }
}
