import { CategoryForm } from "@/components/editor-forms";
import { requireEditor } from "@/lib/auth";

export default async function NewCategoryPage() {
  await requireEditor();
  return <div className="form-panel"><h1>Un nuevo tema.</h1><CategoryForm /></div>;
}
