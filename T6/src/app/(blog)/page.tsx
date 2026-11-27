import Image from "next/image";
import Link from "next/link";
import { getArticles, getCategories } from "@/lib/blog";
import { ArticleCard } from "@/components/article-card";
import { EmptyState } from "@/components/empty-state";

export const revalidate = 60;

export default async function Home() {
  const [articles, categories] = await Promise.all([getArticles(), getCategories()]);
  const featured = articles[0];
  return (
    <>
      <section className="hero shell">
        <div className="hero-copy">
          <p className="eyebrow"><span className="status-dot" /> UNA BITÁCORA DEL MUNDO DIGITAL</p>
          <h1>Tecnología<br />con <em>intención.</em></h1>
          <p className="hero-description">Entre líneas de código y grandes preguntas.<br className="desktop-break" />
            Ideas para crear, entender y hacer las cosas un poco mejor.</p>
          <Link className="button primary" href="/articulos">Encuentra tu próxima idea <span aria-hidden>↗</span></Link>
          <div className="hero-footnote"><span className="mini-code" aria-hidden>{"{ }"}</span> Para quienes nunca dejan de aprender.</div>
        </div>
        <div className="hero-art">
          <div className="orbit orbit-one" /><div className="orbit orbit-two" />
          <span className="art-label">EL FUTURO SE CONSTRUYE</span>
          <div className="terminal">
            <div className="terminal-top"><span /><span /><span /><small>ideas.ts</small></div>
            <div className="terminal-code"><span className="muted">{"// empieza con curiosidad"}</span><br /><br />
              <span className="lime">const</span> futuro = {"{"}<br />
              &nbsp; aprender: <span className="sand">true</span>,<br />
              &nbsp; compartir: <span className="sand">true</span>,<br />
              &nbsp; crear: <span className="sand">&apos;sin límites&apos;</span><br />
              {"}"};<br /><br /><span className="lime">construir</span>(futuro);<span className="cursor" />
            </div>
          </div>
          <div className="floating-note"><span aria-hidden>✳</span> Menos ruido.<br /><strong>Más ideas.</strong></div>
          <span className="art-bottom">01 / EXPLORAR · APRENDER · CREAR</span>
        </div>
      </section>
      <div className="topic-strip"><div className="shell topic-inner"><span>EN NUESTRO RADAR</span>
        {categories.map((category) => <Link key={category.id} href={`/categorias/${category.slug}`}>{category.name}<span aria-hidden>↗</span></Link>)}
      </div></div>
      {featured && <section className="shell featured-section">
        <div className="section-heading"><p className="eyebrow">LA IDEA DE LA SEMANA</p><span className="edition">SELECCIÓN EDITORIAL / 01</span></div>
        <article className="featured">
          <Link href={`/articulos/${featured.slug}`} className="featured-image" aria-label={`Leer ${featured.title}`}>
            <Image src={`/covers/${featured.cover}.svg`} width={800} height={500} alt=""
              sizes="(max-width: 680px) 100vw, 50vw" />
            <span className="featured-sticker">PARA PENSAR<br /><strong>Y CONSTRUIR.</strong></span>
          </Link>
          <div className="featured-copy"><div className="card-meta"><Link href={`/categorias/${featured.category.slug}`}>{featured.category.name}</Link><span>{featured.reading_minutes} min de lectura</span></div>
            <h2><Link href={`/articulos/${featured.slug}`}>{featured.title}</Link></h2>
            <p>{featured.excerpt}</p>
            <Link href={`/articulos/${featured.slug}`} className="text-link">Leer el artículo <span aria-hidden>↗</span></Link>
          </div>
        </article>
      </section>}
      <section className="shell latest-section">
        <div className="section-heading"><div><p className="eyebrow">FRESCO DEL EDITOR</p><h2>Las últimas notas<span className="green-period">.</span></h2></div>
          <Link href="/articulos" className="text-link">Ver todas <span aria-hidden>↗</span></Link>
        </div>
        {articles.length ? <div className="article-grid">{articles.slice(1, 4).map((article) => <ArticleCard key={article.id} article={article} />)}</div>
          : <EmptyState title="Estamos preparando las primeras notas." description="Vuelve pronto para descubrir las ideas de nuestra redacción." />}
      </section>
      <section className="manifesto shell"><span className="manifesto-symbol" aria-hidden>✳</span><div><p className="eyebrow">UNA IDEA SIMPLE</p>
        <h2>La tecnología avanza.<br /><em>La curiosidad nos mueve.</em></h2></div>
        <Link href="/acerca" className="button secondary">Conoce Bitácora <span aria-hidden>↗</span></Link>
      </section>
    </>
  );
}
