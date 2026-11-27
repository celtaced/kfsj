import Image from "next/image";
import Link from "next/link";
import type { Article } from "@/lib/database.types";

export function ArticleCard({ article }: { article: Article }) {
  return (
    <article className="article-card">
      <Link href={`/articulos/${article.slug}`} className="card-cover" aria-label={`Leer ${article.title}`}>
        <Image src={`/covers/${article.cover}.svg`} width={800} height={500}
          alt="" sizes="(max-width: 680px) 100vw, (max-width: 1000px) 50vw, 33vw" />
        <span className="cover-arrow" aria-hidden>↗</span>
      </Link>
      <div className="card-body">
        <div className="card-meta">
          <Link href={`/categorias/${article.category.slug}`}>{article.category.name}</Link>
          <span>{article.reading_minutes} min de lectura</span>
        </div>
        <h3><Link href={`/articulos/${article.slug}`}>{article.title}</Link></h3>
        <p>{article.excerpt}</p>
        <div className="card-date"><span className="tiny-dot" />Redacción Bitácora
          <time dateTime={article.created_at}>{new Intl.DateTimeFormat("es-SV", {
            day: "numeric", month: "short", timeZone: "UTC",
          }).format(new Date(article.created_at))}</time>
        </div>
      </div>
    </article>
  );
}
