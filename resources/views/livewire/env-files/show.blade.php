<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('View Env File') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="space-y-6">
                        <!-- Header with Actions -->
                        <div class="flex justify-between items-start border-b border-gray-200 dark:border-gray-700 pb-4">
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $envFile->name }}</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Project: {{ $envFile->project_name }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('env-files.edit', $envFile) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Edit
                                </a>
                                <button wire:click="downloadEnvFile" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white">
                                    Download .env
                                </button>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Environment</h4>
                                <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    @if($envFile->environment === 'production') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @elseif($envFile->environment === 'staging') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @endif">
                                    {{ ucfirst($envFile->environment) }}
                                </span>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $envFile->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="col-span-2">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $envFile->updated_at->format('M d, Y H:i') }} ({{ $envFile->updated_at->diffForHumans() }})</p>
                            </div>
                            @if($envFile->description)
                                <div class="col-span-2">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</h4>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $envFile->description }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Content Section -->
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Environment Variables</h4>
                                <button wire:click="toggleContent" class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    @if($showContent)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                        Hide Content
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                            <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                        </svg>
                                        Show Content
                                    @endif
                                </button>
                            </div>

                            @if($showContent)
                                <div class="relative">
                                    <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md mb-2">
                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                            <strong>Security Notice:</strong> Content is decrypted for viewing. Be careful when sharing your screen.
                                        </p>
                                    </div>
                                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto"><code class="text-sm font-mono">{{ $envFile->content }}</code></pre>
                                </div>
                            @else
                                <div class="bg-gray-100 dark:bg-gray-900 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-8 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Content is encrypted and hidden for security</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">Click "Show Content" to decrypt and view</p>
                                </div>
                            @endif
                        </div>

                        <!-- Back Button -->
                        <div class="flex justify-end border-t border-gray-200 dark:border-gray-700 pt-4">
                            <a href="{{ route('env-files.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
