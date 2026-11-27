import "server-only";
import { createServerClient } from "@supabase/ssr";
import { cookies } from "next/headers";
import type { Database } from "@/lib/database.types";
import { supabaseConfig } from "./config";

export async function serverClient(writable = false) {
  const store = await cookies();
  const { url, key } = supabaseConfig();
  return createServerClient<Database>(url, key, {
    cookies: {
      getAll: () => store.getAll(),
      setAll(values) {
        // Proxy actualiza la sesion cuando el render del servidor es de solo lectura.
        if (writable) {
          values.forEach(({ name, value, options }) => store.set(name, value, options));
        }
      },
    },
  });
}
