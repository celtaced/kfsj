import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";
import type { Metadata } from "next";
import { getArticle, getArticles } from "@/lib/blog";
import { ArticleCard } from "@/components/article-card";

export const revalidate = 60;

export async function generateStaticParams() {
  return (await getArticles()).map(({ slug }) => ({ slug }));
}

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }): Promise<Metadata> {
  const article = await getArticle((await params).slug);
  if (!article) return { title: "Artículo no encontrado" };
  return { title: article.title, description: article.excerpt,
    openGraph: { type: "article", title: article.title, description: article.excerpt } };
}

export default async function ArticlePage({ params }: { params: Promise<{ slug: string }> }) {
  const article = await getArticle((await params).slug);
  if (!article) notFound();
  const related = (await getArticles()).filter((post) => post.id !== article.id && post.category_id === article.category_id).slice(0, 3);
  return <article className="shell page-section">
    <Link href="/articulos" className="text-link">← Volver al archivo</Link>
    <header className="article-header">
      <div className="card-meta"><Link href={`/categorias/${article.category.slug}`}>{article.category.name}</Link><span>{article.reading_minutes} min de lectura</span></div>
      <h1>{article.title}</h1><p>{article.excerpt}</p>
      <div className="byline"><span className="avatar" aria-hidden>✳</span><div><strong>Redacción Bitácora</strong><br />
        <time dateTime={article.created_at}>{new Intl.DateTimeFormat("es-SV", { dateStyle: "long", timeZone: "UTC" }).format(new Date(article.created_at))}</time></div></div>
    </header>
    <Image className="article-hero-image" src={`/covers/${article.cover}.svg`} width={1200} height={650} alt=""
      sizes="(max-width: 1200px) 100vw, 1200px" />
    <div className="prose">{article.content.split(/\n\s*\n/).map((paragraph, index) => <p key={index}>{paragraph}</p>)}</div>
    <div className="article-end"><span aria-hidden>✳</span> Comparte ideas. Sigue construyendo.</div>
    {related.length > 0 && <section className="related"><div className="section-heading"><h2>Continúa la conversación.</h2></div>
      <div className="article-grid">{related.map((post) => <ArticleCard key={post.id} article={post} />)}</div>
    </section>}
  </article>;
}
