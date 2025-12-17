<?php

namespace App\Livewire\EnvFiles;

use App\Models\EnvFile;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $environment = '';
    
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEnvironment()
    {
        $this->resetPage();
    }

    #[On('env-file-saved')]
    public function refreshList()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $envFile = EnvFile::findOrFail($id);
        
        $this->authorize('delete', $envFile);
        
        $envFile->delete();
        
        session()->flash('message', 'Env file deleted successfully.');
    }

    public function render()
    {
        $query = auth()->user()->envFiles()
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('project_name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->environment) {
            $query->where('environment', $this->environment);
        }

        return view('livewire.env-files.index', [
            'envFiles' => $query->paginate(10),
        ])->layout('layouts.app');
    }
}
