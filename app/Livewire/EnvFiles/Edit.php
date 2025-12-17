<?php

namespace App\Livewire\EnvFiles;

use App\Models\EnvFile;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    public EnvFile $envFile;

    #[Validate('required|string|max:255')]
    public $name;

    #[Validate('required|string|max:255')]
    public $project_name;

    #[Validate('nullable|string')]
    public $description;

    #[Validate('required|string')]
    public $content;

    #[Validate('required|in:development,staging,production')]
    public $environment;

    public function mount(EnvFile $envFile)
    {
        $this->authorize('update', $envFile);
        
        $this->envFile = $envFile;
        $this->name = $envFile->name;
        $this->project_name = $envFile->project_name;
        $this->description = $envFile->description;
        $this->content = $envFile->content;
        $this->environment = $envFile->environment;
    }

    public function save()
    {
        $this->authorize('update', $this->envFile);
        
        $validated = $this->validate();

        $this->envFile->update($validated);

        session()->flash('message', 'Env file updated successfully.');

        $this->dispatch('env-file-saved');

        return redirect()->route('env-files.index');
    }

    public function render()
    {
        return view('livewire.env-files.edit');
    }
}
