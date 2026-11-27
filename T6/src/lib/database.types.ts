export type Category = {
  id: string;
  name: string;
  slug: string;
  description: string;
};

export type Post = {
  id: string;
  category_id: string;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  cover: "code" | "cloud" | "design";
  reading_minutes: number;
  published: boolean;
  created_at: string;
  updated_at: string;
};

type Table<Row, Insert, Relationships extends unknown[] = []> = {
  Row: Row;
  Insert: Insert;
  Update: Partial<Insert>;
  Relationships: Relationships;
};

export type Database = {
  public: {
    Tables: {
      categories: Table<Category, Omit<Category, "id"> & { id?: string }>;
      posts: Table<
        Post,
        Omit<Post, "id" | "created_at" | "updated_at"> & {
          id?: string;
          created_at?: string;
          updated_at?: string;
        },
        [{
          foreignKeyName: "posts_category_id_fkey";
          columns: ["category_id"];
          isOneToOne: false;
          referencedRelation: "categories";
          referencedColumns: ["id"];
        }]
      >;
      editors: Table<
        { user_id: string; created_at: string },
        { user_id: string; created_at?: string }
      >;
    };
    Views: Record<string, never>;
    Functions: Record<string, never>;
    Enums: Record<string, never>;
    CompositeTypes: Record<string, never>;
  };
};

export type Article = Post & { category: Category };
