<div class="app-card p-3">

    <div class="d-flex align-items-start justify-content-between">

        {{-- LEFT --}}
        <div class="d-flex gap-3">

            {{-- CHECKBOX --}}
            <button class="app-check-btn">

                <i class="bi bi-circle"></i>

            </button>

            {{-- CONTENT --}}
            <div>

                <h6 class="fw-semibold mb-1">
                    Finish Offline Sync Engine
                </h6>

                <p class="small mb-2">

                    Build synchronization between
                    IndexedDB and Laravel API.

                </p>

                <div class="d-flex flex-wrap gap-2">

                    <span class="badge app-badge-doing">
                        Doing
                    </span>

                    <span class="badge app-badge-priority">
                        High Priority
                    </span>

                    <span class="badge bg-body-tertiary text-muted">

                        <i class="bi bi-calendar-event"></i>

                        Tomorrow
                    </span>

                </div>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="dropdown">

            <button class="btn app-icon-btn" data-bs-toggle="dropdown">

                <i class="bi bi-three-dots"></i>

            </button>

            <ul class="dropdown-menu dropdown-menu-end">

                <li>
                    <button class="dropdown-item">
                        Edit
                    </button>
                </li>

                <li>
                    <button class="dropdown-item text-danger">
                        Delete
                    </button>
                </li>

            </ul>

        </div>

    </div>

</div>