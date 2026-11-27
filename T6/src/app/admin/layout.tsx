import Link from "next/link";
import type { Metadata } from "next";
import { requireEditor } from "@/lib/auth";
import { signOut } from "@/app/acceso/actions";

export const metadata: Metadata = { title: "Redacción", robots: { index: false, follow: false } };

export default async function AdminLayout({ children }: { children: React.ReactNode }) {
  await requireEditor();
  return <section className="shell page-section">
    <p className="eyebrow">EL TALLER DE IDEAS</p>
    <nav className="admin-tabs" aria-label="Redacción">
      <Link href="/admin">Artículos</Link><Link href="/admin/categorias">Categorías</Link><Link href="/">Ver el blog ↗</Link>
      <form action={signOut}><button className="button secondary">Cerrar sesión</button></form>
    </nav>{children}
  </section>;
}
