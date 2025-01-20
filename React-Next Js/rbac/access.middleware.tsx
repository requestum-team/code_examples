import { FC, useMemo } from "react";
import { Outlet, useLocation, useParams } from "react-router-dom";

import { getEMPath } from "app/Router/RouterHelper";
import NoAccess from "common/access-control/no-access/NoAccess";
import NoSubscription from "common/access-control/no-subscription/NoSubscription";
import { useFeatures } from "common/access-control/useFeatures";

import { CompanyFeatures, FEATURES } from "./RBAC.types";

// Access Middleware for Equity Management
const AccessMiddlewareEM: FC = () => {
    const { pathname } = useLocation();
    const { companyId } = useParams<{ companyId: string }>();

    const manageAccess = useMemo(() => {
        return {
            [getEMPath(["ownership", "capTable"], { companyId })]: FEATURES.capTable,
            [getEMPath(["plans", "exercising"], { companyId })]: FEATURES.exercise,
            [getEMPath(["ownership", "financialInstruments"], { companyId })]: FEATURES.issueEquity,
            [getEMPath(["ownership", "valuation"], { companyId })]: FEATURES.valuation,
            [getEMPath(["ownership", "documents"], { companyId })]: FEATURES.documents,
            [getEMPath(["createPlan", "start"], { companyId })]: FEATURES.managePlans,
            [getEMPath(["createOneOffPlan", "planReceiver"], { companyId })]: FEATURES.managePlans,
        };
    }, [companyId]);

    const subscriptionAccessKeys = useMemo(() => {
        const path = {
            [getEMPath(["ownership", "capTable"], { companyId })]: [CompanyFeatures.Free],
            [getEMPath(["plans", "exercising"], { companyId })]: [CompanyFeatures.Growth],
            [getEMPath(["ownership", "financialInstruments"], { companyId })]: [CompanyFeatures.Growth],
            [getEMPath(["ownership", "documents"], { companyId })]: [CompanyFeatures.Growth],
        };

        return path[pathname.replace(/\/$/, "")];
    }, [companyId, pathname]);

    const accessKey = manageAccess[pathname.replace(/\/$/, "")];

    const { hasSubscriptionAccess, hasSysAdminAccess, hasFullAccess, hasViewAccess } = useFeatures(
        accessKey,
        subscriptionAccessKeys
    );

    if (hasSysAdminAccess) return <Outlet />;

    if (!hasSubscriptionAccess && subscriptionAccessKeys) {
        return <NoSubscription />;
    }

    if (accessKey && !hasFullAccess && !hasViewAccess) {
        return <NoAccess />;
    }

    return <Outlet />;
};

export default AccessMiddlewareEM;
