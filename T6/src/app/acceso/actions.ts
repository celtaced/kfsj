"use server";

import { z } from "zod";
import { redirect } from "next/navigation";
import { serverClient } from "@/lib/supabase/server";
import type { ActionState } from "@/lib/validation";

const credentials = z.object({
  email: z.email("Escribe un correo valido."),
  password: z.string().min(8, "Usa una contraseña de al menos 8 caracteres.").max(128),
});

export async function authenticate(_: ActionState, form: FormData): Promise<ActionState> {
  const parsed = credentials.safeParse({ email: form.get("email"), password: form.get("password") });
  if (!parsed.success) {
    return { message: "Revisa los campos.", errors: z.flattenError(parsed.error).fieldErrors };
  }
  const client = await serverClient(true);
  const { error } = await client.auth.signInWithPassword(parsed.data);
  if (error) {
    console.warn("Inicio de sesion rechazado", error.code);
    return { message: "No pudimos iniciar sesion. Revisa tus credenciales y confirma tu correo." };
  }
  redirect("/admin");
}

export async function register(_: ActionState, form: FormData): Promise<ActionState> {
  const parsed = credentials.safeParse({ email: form.get("email"), password: form.get("password") });
  if (!parsed.success) {
    return { message: "Revisa los campos.", errors: z.flattenError(parsed.error).fieldErrors };
  }
  const site = process.env.NEXT_PUBLIC_SITE_URL;
  if (!site) throw new Error("Configura NEXT_PUBLIC_SITE_URL para confirmar los correos.");
  const client = await serverClient(true);
  const { error } = await client.auth.signUp({
    ...parsed.data,
    options: { emailRedirectTo: `${site}/auth/callback` },
  });
  if (error) {
    console.warn("Registro rechazado", error.code);
    return { message: "No pudimos crear la cuenta. Verifica los datos o espera antes de reintentar." };
  }
  return { message: "Revisa tu correo para confirmar la cuenta. Un administrador deberá concederte acceso editorial." };
}

export async function signOut() {
  const client = await serverClient(true);
  const { error } = await client.auth.signOut();
  if (error) {
    console.error("Error cerrando la sesion", error.message);
    throw new Error("No fue posible cerrar la sesion. Intenta de nuevo.");
  }
  redirect("/acceso");
}
