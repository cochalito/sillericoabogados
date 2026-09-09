@extends('layouts.admin')

@section('title', ($modulo ?? 'Módulo') . ' - Sillerico & Abogados')
@section('header_title', $modulo ?? 'Módulo')

@section('content')
<div class="max-w-4xl mx-auto py-12">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-8 md:p-12 text-center">
        <div class="w-16 h-16 rounded-2xl bg-brand-gold/10 border border-brand-gold/20 flex items-center justify-center mx-auto mb-5 text-brand-gold">
            <i data-lucide="sparkles" class="w-8 h-8"></i>
        </div>
        <h2 class="text-xl font-bold text-slate-800 mb-2">{{ $modulo ?? 'Sección en Desarrollo' }}</h2>
        <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
            Este módulo ya cuenta con su modelo de datos y endpoints de API listos en el backend. La interfaz visual se conectará en la siguiente fase.
        </p>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-medium">
            <i data-lucide="check-circle" class="w-4 h-4 text-brand-green"></i>
            <span>APIs y estructura backend operativas</span>
        </div>
    </div>
</div>
@endsection