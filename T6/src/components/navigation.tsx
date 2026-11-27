"use client";

import Link from "next/link";
import { useState } from "react";
import { usePathname } from "next/navigation";

export function Navigation() {
  const [open, setOpen] = useState(false);
  const pathname = usePathname();
  return (
    <nav aria-label="Principal" className="navigation">
      <button className="menu-toggle" aria-expanded={open} aria-controls="site-menu"
        onClick={() => setOpen(!open)}>Menú {open ? "−" : "+"}</button>
      <div id="site-menu" className={`nav-links ${open ? "is-open" : ""}`}>
        <Link href="/articulos" aria-current={pathname.startsWith("/articulos") ? "page" : undefined}
          onClick={() => setOpen(false)}>Explorar</Link>
        <Link href="/acerca" onClick={() => setOpen(false)}>El proyecto</Link>
        <Link href="/admin" className="nav-admin" onClick={() => setOpen(false)}>Redacción <span aria-hidden>↗</span></Link>
      </div>
    </nav>
  );
}
