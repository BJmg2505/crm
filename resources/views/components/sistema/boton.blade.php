@props(['type' => 'middle', 'title', 'count', 'color' => '#ccc', 'active' => false])

<div class="etapa-item {{ $active ? 'active' : '' }}"
    style="--bg-color: {{ $color }}; --etapas-total: {{ $totalEtapas ?? 12 }};" role="progressbar"
    aria-valuenow="{{ $count }}" aria-valuemin="0" aria-valuemax="{{ $totalEtapas ?? 12 }}">
    <button {{ $attributes->merge(['class' => 'etapa-btn' . ($active ? ' active' : '')]) }}
        aria-current="{{ $active ? 'true' : 'false' }}">
        <div class="etapa-content">
            <span class="etapa-title" aria-label="{{ __('Etapa') }}: {{ $title }}">
                {{ $title }}
            </span>
            <span class="etapa-count" aria-hidden="true">
                {{ $count }}
            </span>
        </div>
    </button>
</div>

<style>
    .etapa-grid-container {
        width: 97%;
        padding: 0.5rem 0;
    }

    .etapa-grid {
        display: flex;
        width: 100%;
        justify-content: center;
        padding: 0 2%;
    }

    .etapa-item {
        position: relative;
        height: 80px;
        flex: 1 0 auto;
        min-width: calc(100% / var(--etapas-total, 12) - 2%);
        max-width: calc(100% / var(--etapas-total, 12) + 2%);
        margin-right: -1.8%;
    }

    .etapa-btn {
        width: 85%;
        height: 85%;
        border: none;
        background: var(--bg-color);
        color: #ffffff;
        font-weight: 700;
        position: relative;
        padding: 0 5px;
        transition: all 0.3s ease;
        cursor: pointer;
        clip-path: polygon(93% 0, 100% 50%, 93% 100%, 0% 100%, 7% 50%, 0% 0%);
    }

    /* Primer y último elemento */
    .etapa-item:first-child .etapa-btn {
        clip-path: polygon(93% 0, 100% 50%, 93% 100%, 0% 100%, 0% 50%, 0% 0%);
        margin-left: 1%;
    }

    .etapa-item:last-child {
        margin-right: 0;
    }

    .etapa-item:last-child .etapa-btn {
        clip-path: polygon(100% 0, 100% 50%, 100% 100%, 0% 100%, 7% 50%, 0% 0%);
    }

    /* Estados activos */
    .etapa-btn.active {
        color: #ffffff;
        z-index: 2;
        transform: scale(1.18);
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.3);
    }

    /* Contenido */
    .etapa-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
        overflow: hidden;
    }



    .etapa-title {
        font-size: clamp(0.5rem, 1.5vw, 0.7rem);
        line-height: 1.2;
        text-align: center;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .etapa-count {
        font-size: clamp(0.8rem, 2.5vw, 1.1rem);
        font-weight: 900;
        margin-top: 2px;
    }

    /* Media Queries específicas */
    @media (max-width: 768px) {
        .etapa-title {
            -webkit-line-clamp: 1;
        }

        .etapa-item {
            height: 70px;
        }
    }

    @media (max-width: 480px) {
        .etapa-item {
            height: 60px;
        }

        .etapa-btn {
            clip-path: polygon(90% 0, 100% 50%, 90% 100%, 0% 100%, 10% 50%, 0% 0%);
        }

        .etapa-item:first-child .etapa-btn {
            clip-path: polygon(90% 0, 100% 50%, 90% 100%, 0% 100%, 0% 50%, 0% 0%);
        }

        .etapa-item:last-child .etapa-btn {
            clip-path: polygon(100% 0, 100% 50%, 100% 100%, 0% 100%, 10% 50%, 0% 0%);
        }
    }
</style>
