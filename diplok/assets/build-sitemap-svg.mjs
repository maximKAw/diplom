import fs from "fs";
const svg = `<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1100 720" width="1100" height="720">
  <defs>
    <marker id="arr" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto" markerUnits="strokeWidth">
      <path d="M0,0 L0,6 L9,3 z" fill="#2563eb"/>
    </marker>
    <marker id="arrUp" markerWidth="10" markerHeight="10" refX="1" refY="3" orient="auto" markerUnits="strokeWidth">
      <path d="M9,0 L9,6 L0,3 z" fill="#2563eb"/>
    </marker>
    <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="1" stdDeviation="2" flood-opacity="0.08"/>
    </filter>
  </defs>
  <rect width="100%" height="100%" fill="#ffffff"/>
  <style type="text/css"><![CDATA[
    .box { fill: #f8fafc; stroke: #94a3b8; stroke-width: 1.25; filter: url(#shadow); }
    .txt { font-family: "Segoe UI", system-ui, sans-serif; font-size: 12px; fill: #0f172a; }
    .edge { fill: none; stroke: #64748b; stroke-width: 1.2; }
  ]]></style>
  <g id="nodes">
    <rect class="box" x="430" y="16" width="240" height="40" rx="4"/>
    <text class="txt" x="550" y="41" text-anchor="middle">«Главная страница»</text>
    <rect class="box" x="430" y="100" width="240" height="40" rx="4"/>
    <text class="txt" x="550" y="125" text-anchor="middle">«Деятельность эксперта»</text>
    <rect class="box" x="120" y="200" width="200" height="44" rx="4"/>
    <text class="txt" x="220" y="228" text-anchor="middle">«Книжный фонд»</text>
    <rect class="box" x="780" y="200" width="220" height="44" rx="4"/>
    <text class="txt" x="890" y="228" text-anchor="middle">«Реализованные инициативы»</text>
    <rect class="box" x="410" y="310" width="280" height="40" rx="4"/>
    <text class="txt" x="550" y="335" text-anchor="middle">«Работа с читателями»</text>
    <rect class="box" x="40" y="430" width="260" height="48" rx="4"/>
    <text class="txt" x="170" y="452" text-anchor="middle" font-size="11px">«Справочно-библиографическая</text>
    <text class="txt" x="170" y="466" text-anchor="middle" font-size="11px">работа»</text>
    <rect class="box" x="430" y="432" width="240" height="44" rx="4"/>
    <text class="txt" x="550" y="458" text-anchor="middle">«Техническое оснащение»</text>
    <rect class="box" x="780" y="432" width="280" height="44" rx="4"/>
    <text class="txt" x="920" y="458" text-anchor="middle" font-size="11px">«В помощь школьным программам»</text>
    <rect class="box" x="200" y="580" width="220" height="40" rx="4"/>
    <text class="txt" x="310" y="605" text-anchor="middle">«Социальное партнёрство»</text>
    <rect class="box" x="780" y="580" width="200" height="40" rx="4"/>
    <text class="txt" x="880" y="605" text-anchor="middle">«Админ-панель»</text>
  </g>
  <g id="edges" fill="none" stroke="#64748b" stroke-width="1.2">
    <path class="edge" d="M550 56 L550 100" marker-end="url(#arr)"/>
    <path class="edge" d="M450 56 L450 295 L550 295 L550 310" marker-end="url(#arr)"/>
    <path class="edge" d="M650 56 L1040 56 L1040 432" marker-end="url(#arr)"/>
    <path class="edge" d="M430 120 L220 120 L220 200" marker-end="url(#arr)"/>
    <path class="edge" d="M670 120 L890 120 L890 200" marker-end="url(#arr)"/>
    <path class="edge" d="M220 244 L220 280 L550 280 L550 310" marker-end="url(#arr)"/>
    <path class="edge" d="M220 244 L220 400 L170 400 L170 430" marker-end="url(#arr)"/>
    <path class="edge" d="M890 244 L890 280 L550 280" marker-end="url(#arr)"/>
    <path class="edge" d="M550 350 L550 432" marker-end="url(#arr)"/>
    <path class="edge" d="M690 330 L920 330 L920 432" marker-end="url(#arr)"/>
    <path class="edge" d="M550 476 L550 520 L310 520 L310 580" marker-end="url(#arr)"/>
    <path class="edge" d="M920 476 L920 580" marker-end="url(#arr)"/>
    <path class="edge" d="M310 580 L310 500 L170 500 L170 478" marker-end="url(#arrUp)"/>
  </g>
</svg>
`;
const p = new URL("./sitemap-biblioteka-shibc.svg", import.meta.url);
fs.writeFileSync(p, svg, "utf8");
console.log("written", p.pathname);
