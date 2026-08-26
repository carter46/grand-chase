@php
    $autoMigrationErrors = session('auto_migration_errors', []);
    $autoMigrationSuccess = session('auto_migration_success', []);
    if (!empty($autoMigrationSuccess)) {
        session()->forget('auto_migration_success');
    }
@endphp

@if (!empty($autoMigrationErrors))
    <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3 mb-0" role="alert">
        <strong><i class="fas fa-database"></i> Database update failed</strong>
        <p class="mb-2 mt-2 small">
            Pending schema updates could not be applied. Fix the issue below, then reload any admin page to retry.
        </p>
        <ul class="mb-0 pl-3 small">
            @foreach ($autoMigrationErrors as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@elseif (!empty($autoMigrationSuccess))
    <div class="alert alert-success alert-dismissible fade show mx-3 mt-3 mb-0" role="alert">
        <strong><i class="fas fa-check-circle"></i> Database updated</strong>
        <ul class="mb-0 mt-2 pl-3 small">
            @foreach ($autoMigrationSuccess as $ok)
                <li>{{ $ok }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
