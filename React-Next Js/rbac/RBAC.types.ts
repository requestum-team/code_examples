// Note: Enums and types are used to define access control parameters that apply to different modules within the app

// different modules within the app
export type FeaturesNames =
    | "capTable"
    | "exercise"
    | "issueEquity"
    | "valuation"
    | "documents"
    | "managePlans";

export const ACCESS_LEVELS = {
    NO_ACCESS: 0,
    VIEW: 1,
    FULL: 2,
} as const;

// accordance between API data
export const FEATURES: Record<FeaturesNames, number> = {
    capTable: 1,
    exercise: 2,
    issueEquity: 3,
    valuation: 4,
    documents: 5,
    managePlans: 6,
} as const;

// Ids connecting stripe subscriptions for free (1) and paid (2) plans
export enum CompanyFeatures {
    Free = 1,
    Growth = 2,
}