"use client";

import Link from "next/link";
import { useActionState, useState } from "react";
import { savePost, saveCategory, deletePost, deleteCategory } from "@/app/admin/actions";
import type { Category, Post } from "@/lib/database.types";
import type { ActionState } from "@/lib/validation";

function FieldError({ state, field }: { state: ActionState; field: string }) {
  return <span id={`${field}-error`} className="field-error">{state.errors?.[field]?.[0]}</span>;
}

export function PostForm({ post, categories }: { post?: Post; categories: Category[] }) {
  const [state, action, pending] = useActionState(savePost.bind(null, post?.id ?? null), { message: "" });
  return <form action={action} className="editor-form">
    <div className="field"><label htmlFor="title">Título</label><input name="title" id="title" defaultValue={post?.title} minLength={5} maxLength={140} required aria-describedby="title-error" /><FieldError state={state} field="title" /></div>
    <div className="form-grid">
      <div className="field"><label htmlFor="slug">Slug</label><input name="slug" id="slug" defaultValue={post?.slug} maxLength={100} pattern="[a-z0-9]+(-[a-z0-9]+)*" required aria-describedby="slug-help slug-error" />
        <small id="slug-help">Ejemplo: mi-primer-articulo. Define la URL pública.</small><FieldError state={state} field="slug" /></div>
      <div className="field"><label htmlFor="category_id">Categoría</label><select id="category_id" name="category_id" defaultValue={post?.category_id ?? ""} required aria-describedby="category_id-error">
        <option value="" disabled>Selecciona un tema</option>{categories.map((item) => <option key={item.id} value={item.id}>{item.name}</option>)}
      </select><FieldError state={state} field="category_id" /></div>
    </div>
    <div className="field"><label htmlFor="excerpt">Resumen</label><textarea name="excerpt" id="excerpt" rows={3} defaultValue={post?.excerpt} minLength={20} maxLength={300} required aria-describedby="excerpt-error" /><FieldError state={state} field="excerpt" /></div>
    <div className="field"><label htmlFor="content">Artículo</label><textarea name="content" id="content" rows={14} defaultValue={post?.content} minLength={80} maxLength={30000} required aria-describedby="content-help content-error" />
      <small id="content-help">Texto plano. Separa los párrafos con una línea vacía; no se ejecuta HTML.</small><FieldError state={state} field="content" /></div>
    <div className="form-grid">
      <div className="field"><label htmlFor="cover">Ilustración</label><select id="cover" name="cover" defaultValue={post?.cover ?? "code"}>
        <option value="code">Código / verde</option><option value="cloud">Cloud / lavanda</option><option value="design">Diseño / terracota</option>
      </select><FieldError state={state} field="cover" /></div>
      <div className="field"><label htmlFor="reading_minutes">Minutos de lectura</label><input id="reading_minutes" name="reading_minutes" type="number" min={1} max={60} defaultValue={post?.reading_minutes ?? 5} required aria-describedby="reading_minutes-error" /><FieldError state={state} field="reading_minutes" /></div>
    </div>
    <label className="checkbox-field"><input type="checkbox" name="published" defaultChecked={post?.published ?? false} />Publicar (visible para todos)</label>
    {state.message && <p className="form-message" role="alert">{state.message}</p>}
    <div className="row-actions"><button className="button primary" disabled={pending || !categories.length}>{pending ? "Guardando..." : "Guardar artículo ↗"}</button><Link href="/admin" className="button secondary">Cancelar</Link></div>
    {!categories.length && <p className="form-message">Crea una categoría antes de escribir tu primer artículo.</p>}
  </form>;
}

export function CategoryForm({ category }: { category?: Category }) {
  const [state, action, pending] = useActionState(saveCategory.bind(null, category?.id ?? null), { message: "" });
  return <form action={action} className="editor-form">
    <div className="field"><label htmlFor="name">Nombre</label><input id="name" name="name" defaultValue={category?.name} minLength={2} maxLength={60} required aria-describedby="name-error" /><FieldError state={state} field="name" /></div>
    <div className="field"><label htmlFor="slug">Slug</label><input id="slug" name="slug" defaultValue={category?.slug} maxLength={100} pattern="[a-z0-9]+(-[a-z0-9]+)*" required aria-describedby="slug-error" /><FieldError state={state} field="slug" /></div>
    <div className="field"><label htmlFor="description">Descripción</label><textarea id="description" name="description" rows={3} defaultValue={category?.description} minLength={10} maxLength={240} required aria-describedby="description-error" /><FieldError state={state} field="description" /></div>
    {state.message && <p className="form-message" role="alert">{state.message}</p>}
    <div className="row-actions"><button className="button primary" disabled={pending}>{pending ? "Guardando..." : "Guardar categoría ↗"}</button><Link href="/admin/categorias" className="button secondary">Cancelar</Link></div>
  </form>;
}

export function DeleteForm({ id, kind }: { id: string; kind: "post" | "category" }) {
  const [confirming, setConfirming] = useState(false);
  const [state, action, pending] = useActionState(
    (kind === "post" ? deletePost : deleteCategory).bind(null, id), { message: "" },
  );
  return <div className="delete-panel">
    {!confirming ? <button className="delete-button" onClick={() => setConfirming(true)}>Eliminar</button>
      : <form action={action}><div className="row-actions"><button className="delete-button" disabled={pending}>{pending ? "Eliminando..." : "Confirmar eliminación"}</button>
        <button className="delete-button" type="button" onClick={() => setConfirming(false)}>Cancelar</button></div></form>}
    {state.message && <span className="field-error" role="status">{state.message}</span>}
  </div>;
}
