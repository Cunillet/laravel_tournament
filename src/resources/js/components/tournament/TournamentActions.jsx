/**
 * Conditional action buttons for tournament management.
 *
 * All event handlers are passed as callbacks — no router calls here.
 */
export default function TournamentActions({
    canJoin, canLeave, canStart, canCreateRound, hasActiveRound, canClose,
    onJoin, onLeave, onStart, onCreateRound, onCloseRound, onClose,
}) {
    return (
        <div className="game-edit__section" style={{ display: 'flex', gap: '0.5rem', flexWrap: 'wrap' }}>
            {canJoin && (
                <button className="btn btn--primary" onClick={onJoin}>
                    Unirse al torneo
                </button>
            )}
            {canLeave && (
                <button className="btn btn--secondary" onClick={onLeave}>
                    Salir del torneo
                </button>
            )}
            {canStart && (
                <button className="btn btn--primary" onClick={onStart}>
                    Iniciar torneo
                </button>
            )}
            {canCreateRound && (
                <button className="btn btn--primary" onClick={onCreateRound}>
                    + Crear ronda
                </button>
            )}
            {hasActiveRound && (
                <button className="btn btn--secondary" onClick={onCloseRound}>
                    Cerrar ronda activa
                </button>
            )}
            {canClose && (
                <button className="btn btn--secondary" style={{ color: '#ef4444' }} onClick={onClose}>
                    Cerrar torneo
                </button>
            )}
        </div>
    );
}
