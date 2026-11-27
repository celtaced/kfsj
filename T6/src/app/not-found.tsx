import Link from "next/link";

export default function NotFound() {
  return <section className="shell page-section empty-state">
    <p className="eyebrow">404 / FUERA DEL MAPA</p><h1>Esta página todavía no es una idea.</h1>
    <p>El artículo no existe, dejó de estar publicado o el enlace cambió.</p>
    <Link href="/articulos" className="button primary">Explorar otras notas <span aria-hidden>↗</span></Link>
  </section>;
}
