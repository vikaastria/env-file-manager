<?php

namespace App\Livewire\EnvFiles;

use App\Models\EnvFile;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Create extends Component
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|string|max:255')]
    public $project_name = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|string')]
    public $content = '';

    #[Validate('required|in:development,staging,production')]
    public $environment = 'production';

    public function save()
    {
        $this->authorize('create', EnvFile::class);
        
        $validated = $this->validate();

        EnvFile::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        session()->flash('message', 'Env file created successfully.');

        $this->dispatch('env-file-saved');

        return redirect()->route('env-files.index');
    }

    public function render()
    {
        return view('livewire.env-files.create')->layout('layouts.app');
    }
}
