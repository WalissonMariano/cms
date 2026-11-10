@php
    use App\Models\History;

    $histories = $histories ?? collect();
@endphp

<div class="form-page-card form-page-card--section form-page-history">
    <h2 class="form-page-section-title">Histórico de alterações</h2>
    <p class="form-page-hint">Registro das ações realizadas neste cadastro.</p>

    @if ($histories->isEmpty())
        <p class="form-page-history-empty">Nenhuma ação registrada ainda.</p>
    @else
        <ul class="form-page-history-list">
            @foreach ($histories as $history)
                <li class="form-page-history-item">
                    <div class="form-page-history-item-info">
                        <strong>{{ History::actionLabel($history->action) }}</strong>
                        <span>
                            por {{ $history->user?->name ?? '—' }}
                            em {{ $history->created_at?->format('d/m/Y H:i') ?? '—' }}
                        </span>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
