import Link from "next/link";
import { notFound } from "next/navigation";
import type { Metadata } from "next";
import { getCategories, getArticles } from "@/lib/blog";
import { ArticleCard } from "@/components/article-card";
import { EmptyState } from "@/components/empty-state";

export const revalidate = 60;

export async function generateStaticParams() {
  return (await getCategories()).map(({ slug }) => ({ slug }));
}

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }): Promise<Metadata> {
  const { slug } = await params;
  const category = (await getCategories()).find((item) => item.slug === slug);
  return { title: category?.name ?? "Categoría no encontrada", description: category?.description };
}

export default async function CategoryPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const [categories, articles] = await Promise.all([getCategories(), getArticles()]);
  const category = categories.find((item) => item.slug === slug);
  if (!category) notFound();
  const filtered = articles.filter((post) => post.category_id === category.id);
  return <div className="shell page-section">
    <Link href="/articulos" className="text-link">← Todos los temas</Link>
    <div className="category-heading"><p className="eyebrow">UNA NUEVA PERSPECTIVA</p>
      <h1 className="page-title">{category.name}<span className="green-period">.</span></h1><p>{category.description}</p></div>
    {filtered.length ? <div className="article-grid">{filtered.map((post) => <ArticleCard key={post.id} article={post} />)}</div>
      : <EmptyState title="Esta categoría está por estrenarse." description="Nuestra redacción está preparando nuevas notas sobre este tema." />}
  </div>;
}
