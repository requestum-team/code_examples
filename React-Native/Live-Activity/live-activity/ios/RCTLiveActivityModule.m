// RCTLiveActivityModule.m
#import "nativeModules-Bridging-Header.h"


@interface RCT_EXTERN_MODULE(LiveActivity, NSObject)
RCT_EXTERN_METHOD(endActivity:(NSString)id withResolver:(RCTPromiseResolveBlock)resolve withRejecter:(RCTPromiseRejectBlock)reject)
RCT_EXTERN_METHOD(listAllActivities:(RCTPromiseResolveBlock)resolve withRejecter:(RCTPromiseRejectBlock)reject)
RCT_EXTERN_METHOD(updateActivity:(NSString)id emoji:(NSString)emoji withResolver:(RCTPromiseResolveBlock)resolve withRejecter:(RCTPromiseRejectBlock)reject)



@end

@interface RCT_EXTERN_MODULE(LiveActivitiModule, RCTEventEmitter)

RCT_EXTERN_METHOD(supportedEvents)

@end
