import "server-only";
import { redirect } from "next/navigation";
import { serverClient } from "./supabase/server";

export async function requireEditor() {
  const client = await serverClient();
  const { data: { user }, error: authError } = await client.auth.getUser();
  if (authError && authError.status !== 400 && authError.status !== 401 && authError.name !== "AuthSessionMissingError") {
    console.error("Error verificando la sesion", authError.message);
    throw new Error("No fue posible verificar tu sesion.");
  }
  if (!user) redirect("/acceso");
  const { data, error } = await client.from("editors").select("user_id").eq("user_id", user.id).maybeSingle();
  if (error) {
    console.error("Error verificando permisos editoriales", error.message);
    throw new Error("No fue posible verificar tus permisos.");
  }
  if (!data) redirect("/acceso?estado=sin-permisos");
  return { client, user };
}
