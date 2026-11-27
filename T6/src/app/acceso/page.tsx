import { AccessForm } from "@/components/access-form";
import { signOut } from "./actions";
import type { Metadata } from "next";

export const metadata: Metadata = { title: "Acceso editorial", robots: { index: false, follow: false } };

export default async function AccessPage({ searchParams }: { searchParams: Promise<{ estado?: string }> }) {
  const { estado } = await searchParams;
  return <section className="shell page-section">
    <div className="access-panel"><p className="eyebrow">DETRÁS DE LAS IDEAS</p>
      <h1 className="page-title">Bienvenido<br /><em>a la redacción.</em></h1>
      <p>Un espacio para dar forma a lo que quieres compartir.</p>
      {estado === "sin-permisos" && <div className="form-message" role="status">
        Tu cuenta está autenticada, pero no tiene permisos editoriales. Un administrador debe agregarte a la tabla editors.
        <form action={signOut}><button className="button secondary">Cerrar sesión</button></form>
      </div>}
      {estado === "confirmacion-error" && <p className="form-message" role="alert">No se pudo confirmar la cuenta. El enlace puede haber expirado.</p>}
      <AccessForm />
      <p className="access-note">El registro no concede permisos para publicar. Tus credenciales se verifican con Supabase Auth.</p>
    </div>
  </section>;
}
