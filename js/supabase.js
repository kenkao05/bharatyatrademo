import { createClient } from "https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2/+esm";

const SUPABASE_URL = "https://hmobboiupblojankdaah.supabase.co";
const SUPABASE_ANON_KEY = "sb_publishable_1ohSSqvp2O374buCm3XNBA_aRd8U9H6";

export const supabaseClient = createClient(
  SUPABASE_URL,
  SUPABASE_ANON_KEY
);
