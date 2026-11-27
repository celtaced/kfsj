import Link from "next/link";
import { requireEditor } from "@/lib/auth";
import { DeleteForm } from "@/components/editor-forms";

export default async function CategoriesAdmin({ searchParams }: { searchParams: Promise<{ guardado?: string }> }) {
  const { client } = await requireEditor();
  const [result, query] = await Promise.all([client.from("categories").select("*").order("name"), searchParams]);
  if (result.error) {
    console.error("No se pudieron cargar las categorias", result.error.message);
    throw new Error("No se pudieron cargar las categorias.");
  }
  return <>
    <div className="section-heading"><h1>Organiza las ideas.</h1><Link href="/admin/categorias/nueva" className="button primary">Nueva categoría +</Link></div>
    {query.guardado === "1" && <p className="form-message" role="status">Categoría guardada.</p>}
    <ul className="editor-list">{result.data.map((item) => <li className="editor-row" key={item.id}>
      <div><h2>{item.name}</h2><p>{item.description}</p></div>
      <div className="row-actions"><Link className="text-link" href={`/categorias/${item.slug}`}>Ver ↗</Link><Link className="button secondary" href={`/admin/categorias/${item.id}/editar`}>Editar</Link><DeleteForm kind="category" id={item.id} /></div>
    </li>)}</ul>
    {!result.data.length && <p>No hay categorías todavía.</p>}
  </>;
}
