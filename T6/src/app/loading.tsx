export default function Loading() {
  return <div className="shell page-section loading-state" role="status" aria-label="Cargando contenido">
    <p className="eyebrow">PREPARANDO TU PRÓXIMA IDEA</p>
    <div className="skeleton skeleton-title" />
    <div className="article-grid">{[1, 2, 3].map((id) => <div key={id}>
      <div className="skeleton skeleton-cover" /><div className="skeleton skeleton-line" /><div className="skeleton skeleton-line short" />
    </div>)}</div><span className="sr-only">Cargando...</span>
  </div>;
}
