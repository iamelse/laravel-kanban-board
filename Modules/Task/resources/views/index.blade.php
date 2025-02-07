@extends('admin.layouts.app')

@section('content')
<div id="main-content">
    <div class="page-heading">  
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tasks</h3>
                    <p class="text-subtitle text-muted">View and manage all tasks.</p>
                </div>
            </div>
        </div>        
    </div>

    <!-- Kanban Board Start -->
    <section class="section min-vh-100">
        <div class="kanban-container overflow-auto">
            <kanban-board :initial-data="{{ $tasks }}"></kanban-board>
        </div>
    </section>        
    <!-- Kanban Board End -->
</div>
@endsection

@push('scripts')
    @vite(['resources/js/app.js']) 
@endpush