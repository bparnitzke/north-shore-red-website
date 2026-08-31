export type ImpactMetric = {
  value: string;
  label: string;
  detail: string;
  verification: string;
};

// TODO(client): Every figure below is provisional and must be verified against client records before launch.
export const impactMetrics: ImpactMetric[] = [
  {
    value: "~5,000",
    label: "handwritten postcards",
    detail: "Volunteer voter-contact work reported on the existing site.",
    verification: "Client verification required before launch."
  },
  {
    value: "~$14,300",
    label: "contributed to candidates",
    detail: "Candidate support figure reported for the 2024-2025 campaign season.",
    verification: "Client verification and legal review required before launch."
  },
  {
    value: "23",
    label: "campaign events",
    detail: "Events with candidates and surrogates reported on the existing site.",
    verification: "Client verification required before launch."
  },
  {
    value: "Strong",
    label: "canvassing effort",
    detail: "The existing site describes a county-leading canvassing performance.",
    verification: "Replace with precise, verified wording before launch."
  }
];

export const impactNotice =
  "Prototype note: impact figures are provisional and are collected in src/data/impact.ts for client verification before launch.";
