@props(['simple' => false])

@if($simple)
    <footer class="py-12 border-t border-slate-100 text-center bg-white">
        <p class="text-slate-400 text-sm font-medium">&copy; {{ date('Y') }} Surya Andika. All rights reserved.</p>
    </footer>
@else
    <footer class="text-center py-8 text-slate-400 text-sm border-t border-slate-200">
        <p>&copy; {{ date('Y') }} Surya Andika. Informatics Student &amp; Designer.</p>
    </footer>
@endif
