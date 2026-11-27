import Link from "next/link";
import type { Metadata } from "next";

export const metadata: Metadata = { title: "El proyecto" };

export default function AboutPage() {
  return <section className="shell page-section about">
    <p className="eyebrow">HOLA, SOMOS BITÁCORA</p>
    <h1 className="page-title">Un espacio para<br /><em>pensar en voz alta.</em></h1>
    <div className="prose"><p>Creemos que la tecnología se entiende mejor cuando se comparte. Este es un espacio para explorar desarrollo web, arquitecturas cloud y diseño con una mirada práctica y humana.</p>
      <p>Bitácora es un proyecto educativo de la tarea T6 del Módulo 4. Está construido con Next.js App Router, TypeScript y Supabase. Los artículos se consultan desde componentes del servidor; la redacción cuenta con autenticación y permisos RLS.</p>
      <p>Sin fórmulas mágicas. Sin ruido innecesario. Solo ideas para seguir aprendiendo y construir algo que importe.</p></div>
    <Link href="/articulos" className="button primary">Explora las notas <span aria-hidden>↗</span></Link>
  </section>;
}
