<?php

namespace App\Livewire\EnvFiles;

use App\Models\EnvFile;
use Livewire\Component;

class Show extends Component
{
    public EnvFile $envFile;
    public bool $showContent = false;

    public function mount(EnvFile $envFile)
    {
        $this->authorize('view', $envFile);
        $this->envFile = $envFile;
    }

    public function toggleContent()
    {
        $this->showContent = !$this->showContent;
    }

    public function downloadEnvFile()
    {
        $this->authorize('view', $this->envFile);
        
        $filename = str($this->envFile->name)->slug() . '.env';
        
        return response()->streamDownload(function () {
            echo $this->envFile->content;
        }, $filename, [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function render()
    {
        return view('livewire.env-files.show')->layout('layouts.app');
    }
}
