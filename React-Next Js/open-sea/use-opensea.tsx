import {BigNumberish} from "ethers";
import {Chain, OpenSeaSDK, OrderSide} from "opensea-js";
import {isNotNil} from "ramda";
import {useEffect, useState} from "react";
import {toast} from "react-toastify";

import {useApolloClient} from "@apollo/client";
import {SuccessToast, WarningToast} from "@components/atoms";
import {arrayTime2, IS_DEVELOPMENT} from "@constants";
import {OrderDtoFragmentFragmentDoc} from "@graphql/generated";
import {calculateDuration} from "@helpers";
import {useEthersSigner} from "@hooks";

import type {
    AcceptOrderProps,
    CancelOrderProps,
    AcceptOrdersProps,
    CreateListingProps,
    CreateListingsProps,
    CreateListingWithOrderProps,
    CreateOfferProps,
    EditOrderProps,
    UpdateListingsProps
} from "./use-opensea.types"

export const useOpenSea = (accountAddress: string | undefined) => {
    const client = useApolloClient();
    const [openseaSDK, setOpeneaSdk] = useState<OpenSeaSDK | null>(null);

    const signer = useEthersSigner();

    const init = async () => {
        try {
            if (signer) {
                const createdOpenseaSDK = new OpenSeaSDK(signer, {
                    chain: IS_DEVELOPMENT ? Chain.Sepolia : Chain.Mainnet,
                    apiKey: IS_DEVELOPMENT ? undefined : process.env.OPENSEA_API_KEY,
                });
                setOpeneaSdk(createdOpenseaSDK);
            }
        } catch (e) {
            console.error("Error: ", e);
            toast(<WarningToast text="Error initializing OpenSea SDK."/>);
        }
    };

    const createOffer = async ({
                                   tokenAddress,
                                   tokenId,
                                   isSelectedOptionEqualsCustom,
                                   expirationDate,
                                   selectedDuration,
                                   startAmount,
                               }: CreateOfferProps) => {
        if (openseaSDK && accountAddress) {
            let expirationTime: BigNumberish;

            if (isSelectedOptionEqualsCustom) {
                expirationTime = Math.round(expirationDate.getTime() / 1000);
            } else {
                expirationTime = calculateDuration(
                    selectedDuration as unknown as (typeof arrayTime2)[number]
                );
            }

            await openseaSDK.createOffer({
                asset: {
                    tokenId,
                    tokenAddress,
                },
                accountAddress,
                startAmount,
                excludeOptionalCreatorFees: true,
                expirationTime,
            });

            return {expirationTime};
        }
    };

    const createListing = async ({
                                     selectedNft,
                                     onItemListingFulfilled,
                                     onItemListingRejected,
                                 }: CreateListingProps) => {
        if (openseaSDK && accountAddress) {
            const {
                id: nftId,
                tokenId,
                dataName,
                priceToSale: startAmount,
                date,
            } = selectedNft;

            const id = String(nftId);
            const tokenAddress = id.split(".")[1];

            const formatDate = new Date(date);
            const expirationTime = Math.round(formatDate.getTime() / 1000);

            try {
                await openseaSDK.createListing({
                    accountAddress,
                    startAmount,
                    asset: {
                        tokenId: tokenId!,
                        tokenAddress,
                    },
                    expirationTime,
                });

                onItemListingFulfilled(id);

                toast(
                    <SuccessToast
                        text={`Uploading ${dataName} #${tokenId} to the OpenSea was successful.`}
                    />
                );
            } catch (error) {
                onItemListingRejected(id);

                toast(
                    <WarningToast
                        text={`Error uploading ${dataName} #${tokenId} to the OpenSea`}
                    />
                );
            }
        }
    };
    const createListingWithOrder = async ({
                                              selectedNft,
                                              onItemListingFulfilled,
                                              onItemListingRejected,
                                          }: CreateListingWithOrderProps) => {
        if (openseaSDK && accountAddress) {
            const {
                token: {id: nftId, tokenId, dataName},
                priceToSale: startAmount,
                date,
            } = selectedNft;

            const id = String(nftId);
            const tokenAddress = id.split(".")[1];

            const formatDate = new Date(date);
            const expirationTime = Math.round(formatDate.getTime() / 1000);

            try {
                await openseaSDK.createListing({
                    accountAddress,
                    startAmount,
                    asset: {
                        tokenId,
                        tokenAddress,
                    },
                    expirationTime,
                });

                onItemListingFulfilled(id);

                toast(
                    <SuccessToast
                        text={`Uploading ${dataName} to the OpenSea was successful.`}
                    />
                );
            } catch (error) {
                onItemListingRejected(id);

                toast(
                    <WarningToast text={`Error uploading ${dataName} to the OpenSea`}/>
                );
            }
        }
    };

    const createListings = async ({
                                      selectedNfts,
                                      onItemListingFulfilled,
                                      onItemListingRejected,
                                  }: CreateListingsProps) => {
        for await (const selectedNft of selectedNfts) {
            await createListing({
                selectedNft,
                onItemListingFulfilled,
                onItemListingRejected,
            });
        }
    };

    const cancelOrder = async ({
                                   tokenAddress,
                                   tokenId,
                                   time,
                                   maker,
                                   side = OrderSide.OFFER,
                               }: CancelOrderProps) => {
        if (openseaSDK && accountAddress) {
            const openseaOrder = await openseaSDK.api.getOrder({
                side,
                maker,
                assetContractAddress: tokenAddress,
                tokenId,
                listedBefore: Date.parse(time.toString()),
            });

            await openseaSDK.cancelOrder({accountAddress, order: openseaOrder});
        }
    };

    const editOrder = async ({
                                 tokenAddress,
                                 tokenId,
                                 time,
                                 isSelectedOptionEqualsCustom,
                                 selectedDuration,
                                 startAmount,
                                 expirationDate,
                             }: EditOrderProps) => {
        if (openseaSDK && accountAddress) {
            await cancelOrder({
                tokenAddress,
                tokenId,
                time,
                maker: accountAddress,
                side: OrderSide.OFFER,
            });

            const creationResult = await createOffer({
                isSelectedOptionEqualsCustom,
                tokenAddress,
                tokenId,
                selectedDuration,
                startAmount,
                expirationDate,
            });

            return {newMarketEndedDay: creationResult?.expirationTime};
        }
    };

    const acceptOrder = async ({
                                   side,
                                   maker,
                                   tokenAddress,
                                   tokenId,
                                   onOrderAccepted,
                                   time,
                               }: AcceptOrderProps) => {
        if (openseaSDK && accountAddress) {
            const openseaOrder = await openseaSDK.api.getOrder({
                side,
                maker,
                assetContractAddress: tokenAddress,
                tokenId,
                listedBefore: Date.parse(time.toString()),
            });

            await openseaSDK.fulfillOrder({
                accountAddress,
                order: openseaOrder,
            });

            onOrderAccepted?.();
        }
    };

    const acceptOrders = async ({
                                    selectedOrders,
                                    onOrderAccepted,
                                }: AcceptOrdersProps) => {
        for await (const selectedOrder of selectedOrders) {
            await acceptOrder({...selectedOrder, onOrderAccepted});
        }
    };

    const updateListing = async ({
                                     selectedNfts,
                                     onItemListingFulfilled,
                                     onItemListingRejected,
                                 }: UpdateListingsProps) => {
        if (openseaSDK && accountAddress) {
            for await (const selectedNft of selectedNfts) {
                await cancelOrder({
                    tokenAddress: selectedNft?.token?.contract,
                    tokenId: selectedNft?.token?.tokenId,
                    time: selectedNft?.time,
                    maker: accountAddress,
                    side: OrderSide.LISTING,
                });
                await createListingWithOrder({
                    selectedNft,
                    onItemListingFulfilled,
                    onItemListingRejected,
                });
                const orderFragment = client.readFragment({
                    id: `OrderDto:${selectedNft?.id}`,
                    fragment: OrderDtoFragmentFragmentDoc,
                });
                if (isNotNil(orderFragment)) {
                    client.writeFragment({
                        id: `OrderDto:${selectedNft?.id}`,
                        fragment: OrderDtoFragmentFragmentDoc,
                        data: {
                            ...orderFragment,
                            makeValue: selectedNft?.priceToSale,
                            marketEndedDay: selectedNft?.date,
                        },
                    });
                }
            }
        }
    };

    useEffect(() => {
        init();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [signer]);

    return {
        createListings,
        createOffer,
        cancelOrder,
        editOrder,
        acceptOrder,
        acceptOrders,
        updateListing,
    };
};
