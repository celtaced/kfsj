import { z } from "zod";

const slug = z.string().trim().min(1, "Escribe un slug.").max(100)
  .regex(/^[a-z0-9]+(-[a-z0-9]+)*$/, "Usa minusculas, numeros y guiones.");

export const categorySchema = z.object({
  name: z.string().trim().min(2, "El nombre necesita al menos 2 caracteres.").max(60),
  slug,
  description: z.string().trim().min(10, "Describe la categoria en al menos 10 caracteres.").max(240),
});

export const postSchema = z.object({
  title: z.string().trim().min(5, "El titulo necesita al menos 5 caracteres.").max(140),
  slug,
  category_id: z.uuid("Selecciona una categoria valida."),
  excerpt: z.string().trim().min(20, "Escribe un resumen de al menos 20 caracteres.").max(300),
  content: z.string().trim().min(80, "El contenido necesita al menos 80 caracteres.").max(30000),
  cover: z.enum(["code", "cloud", "design"]),
  reading_minutes: z.coerce.number().int().min(1).max(60),
  published: z.boolean(),
});

export type ActionState = {
  message: string;
  errors?: Record<string, string[] | undefined>;
};
