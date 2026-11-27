import type { Metadata } from "next";
import Link from "next/link";
import { getArticles, getCategories } from "@/lib/blog";
import { ArticleCard } from "@/components/article-card";
import { EmptyState } from "@/components/empty-state";

export const metadata: Metadata = { title: "Explorar artículos" };

export default async function ArticlesPage({
  searchParams,
}: { searchParams: Promise<{ q?: string | string[]; categoria?: string | string[] }> }) {
  const [query, articles, categories] = await Promise.all([searchParams, getArticles(), getCategories()]);
  const q = typeof query.q === "string" ? query.q.slice(0, 120).trim() : "";
  const category = typeof query.categoria === "string" ? query.categoria : "";
  const filtered = articles.filter((post) =>
    (!category || post.category.slug === category) &&
    (!q || `${post.title} ${post.excerpt}`.toLocaleLowerCase("es").includes(q.toLocaleLowerCase("es"))));
  return <div className="shell page-section">
    <p className="eyebrow">EL ARCHIVO DE IDEAS</p><h1 className="page-title">Encuentra algo<br /><em>que te inspire.</em></h1>
    <form action="/articulos" className="search-form" role="search">
      <label className="sr-only" htmlFor="search">Buscar artículos</label>
      <input id="search" name="q" placeholder="Buscar una idea, una herramienta..." defaultValue={q} maxLength={120} />
      <label className="sr-only" htmlFor="category-filter">Categoría</label>
      <select name="categoria" id="category-filter" defaultValue={category}>
        <option value="">Todos los temas</option>{categories.map((item) => <option key={item.id} value={item.slug}>{item.name}</option>)}
      </select><button className="button primary" type="submit">Buscar <span aria-hidden>↗</span></button>
    </form>
    <div className="results-heading"><p>{filtered.length} {filtered.length === 1 ? "artículo" : "artículos"} para explorar</p>
      {(q || category) && <Link href="/articulos" className="text-link">Limpiar filtros</Link>}
    </div>
    {filtered.length ? <div className="article-grid">{filtered.map((post) => <ArticleCard key={post.id} article={post} />)}</div>
      : <EmptyState title="No encontramos esa idea." description="Prueba otra búsqueda o explora todos nuestros artículos." />}
  </div>;
}
