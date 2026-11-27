import type { Metadata } from "next";
import Link from "next/link";
import { Navigation } from "@/components/navigation";
import "./globals.css";

export const metadata: Metadata = {
  metadataBase: new URL(process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3006"),
  title: { default: "Bitácora — Tecnología con intención", template: "%s | Bitácora" },
  description: "Un blog sobre desarrollo, cloud y diseño. Ideas prácticas para construir una web más humana.",
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="es">
      <body>
        <a className="skip-link" href="#contenido">Saltar al contenido</a>
        <header className="site-header">
          <div className="shell header-inner">
            <Link href="/" className="brand" aria-label="Bitácora, inicio">
              <span className="brand-symbol" aria-hidden>✳</span> bitácora<span className="brand-period">.</span>
            </Link>
            <Navigation />
          </div>
        </header>
        <main id="contenido">{children}</main>
        <footer className="site-footer shell">
          <div><Link href="/" className="brand">bitácora.</Link><p>Ideas que merecen salir del editor.</p></div>
          <div className="footer-links"><Link href="/articulos">Explorar artículos</Link><Link href="/acerca">Sobre este proyecto</Link></div>
          <p className="footer-note">Hecho con curiosidad.<br />Next.js + Supabase · T6 / Módulo 4</p>
        </footer>
      </body>
    </html>
  );
}
