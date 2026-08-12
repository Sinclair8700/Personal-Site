<x-page title="Page not found" :noindex="true">
    <x-content class="py-16 text-center text-white flex flex-col items-center gap-4">
        <p class="text-6xl font-semibold">404</p>
        <p class="text-white/70 max-w-md">This page wandered off somewhere. Let's get you back on track.</p>
        <div class="flex flex-wrap gap-4 justify-center mt-4">
            <a href="/" class="rounded-md border-2 border-white px-4 py-2 transition-colors duration-300 hover:border-purple">Home</a>
            <a href="/projects" class="rounded-md border-2 border-white px-4 py-2 transition-colors duration-300 hover:border-purple">Projects</a>
            <a href="/contact" class="rounded-md border-2 border-white px-4 py-2 transition-colors duration-300 hover:border-purple">Contact</a>
        </div>
    </x-content>
</x-page>
