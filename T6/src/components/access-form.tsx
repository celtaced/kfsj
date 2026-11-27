"use client";

import { useActionState, useState } from "react";
import { authenticate, register } from "@/app/acceso/actions";
import type { ActionState } from "@/lib/validation";

function Form({ mode }: { mode: "login" | "register" }) {
  const [state, action, pending] = useActionState<ActionState, FormData>(
    mode === "login" ? authenticate : register, { message: "" },
  );
  return <form action={action} className="editor-form">
    <div className="field"><label htmlFor="email">Correo electrónico</label>
      <input id="email" name="email" type="email" autoComplete="email" required aria-describedby="email-error" />
      <span id="email-error" className="field-error">{state.errors?.email?.[0]}</span>
    </div>
    <div className="field"><label htmlFor="password">Contraseña</label>
      <input id="password" name="password" type="password" autoComplete={mode === "login" ? "current-password" : "new-password"}
        minLength={8} maxLength={128} required aria-describedby="password-error" />
      <span id="password-error" className="field-error">{state.errors?.password?.[0]}</span>
    </div>
    {state.message && <p className="form-message" role="status">{state.message}</p>}
    <button className="button primary" disabled={pending}>{pending ? "Un momento..." : mode === "login" ? "Entrar a la redacción ↗" : "Crear cuenta ↗"}</button>
  </form>;
}

export function AccessForm() {
  const [mode, setMode] = useState<"login" | "register">("login");
  return <div>
    <Form key={mode} mode={mode} />
    <button className="text-link mode-switch" type="button" onClick={() => setMode(mode === "login" ? "register" : "login")}>
      {mode === "login" ? "¿Primera vez? Crea una cuenta" : "Ya tengo una cuenta"}
    </button>
  </div>;
}
