"use client";

import Link from "next/link";
import { useEffect } from "react";

export default function ErrorPage({ error, reset }: { error: Error & { digest?: string }; reset: () => void }) {
  useEffect(() => { console.error("No se pudo cargar la página", error); }, [error]);
  return <section className="shell page-section empty-state" role="alert">
    <p className="eyebrow">HAGAMOS UNA PAUSA</p><h1>No pudimos cargar esta idea.</h1>
    <p>Ocurrió un problema al consultar los datos. Intenta de nuevo en un momento.</p>
    <div className="button-row"><button className="button primary" onClick={reset}>Intentar de nuevo</button>
      <Link href="/" className="button secondary">Volver al inicio</Link></div>
  </section>;
}
