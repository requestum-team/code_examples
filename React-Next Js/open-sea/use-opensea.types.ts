import {BigNumberish} from "ethers";
import {OrderSide} from "opensea-js";
import {
    DropdownOption,
    SelectedListedItemsType,
    SelectedUpdatedListedItemsType,
} from "@types";

export type CreateListingProps = {
    selectedNft: SelectedListedItemsType;
    onItemListingFulfilled: (id: string) => void;
    onItemListingRejected: (id: string) => void;
};

export type CreateListingsProps = {
    selectedNfts: SelectedListedItemsType[];
    onItemListingFulfilled: (id: string) => void;
    onItemListingRejected: (id: string) => void;
};

export type CreateListingWithOrderProps = {
    selectedNft: SelectedUpdatedListedItemsType;
    onItemListingFulfilled: (id: string) => void;
    onItemListingRejected: (id: string) => void;
};

export type CreateOfferProps = {
    tokenAddress: string;
    tokenId: string;
    isSelectedOptionEqualsCustom: boolean;
    expirationDate: Date;
    selectedDuration: DropdownOption["id"];
    startAmount: BigNumberish;
};

export type CancelOrderProps = {
    maker: string;
    tokenAddress: string;
    tokenId: string;
    time: Date;
    side?: OrderSide;
};

export type AcceptOrderProps = {
    side: OrderSide;
    maker: string;
    tokenAddress: string;
    tokenId: string;
    time: Date;
    onOrderAccepted?: () => void;
};

export type AcceptOrdersProps = {
    selectedOrders: AcceptOrderProps[];
    onOrderAccepted?: () => void;
};

export type EditOrderProps = CancelOrderProps & CreateOfferProps;

export type UpdateListingsProps = {
    selectedNfts: SelectedUpdatedListedItemsType[];
    onItemListingFulfilled: (id: string) => void;
    onItemListingRejected: (id: string) => void;
};
