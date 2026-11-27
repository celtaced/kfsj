import Link from "next/link";

export function EmptyState({ title, description }: { title: string; description: string }) {
  return <section className="empty-state">
    <span className="eyebrow">Un espacio para nuevas ideas</span>
    <h2>{title}</h2><p>{description}</p>
    <Link href="/articulos" className="button secondary">Ver todos los artículos <span aria-hidden>↗</span></Link>
  </section>;
}
