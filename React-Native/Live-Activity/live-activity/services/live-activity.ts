import { NativeEventEmitter, NativeModules, Platform } from "react-native";

const myModuleEvt = new NativeEventEmitter(NativeModules.LiveActivitiModule);

const LiveActivity = NativeModules.LiveActivity
  ? NativeModules.LiveActivity
  : new Proxy(
      {},
      {
        get() {
          throw new Error("hmm");
        },
      }
    );

const clearAllActivities = async () => {
  if (!(Platform.OS === "ios")) return;
  myModuleEvt.removeAllListeners("SomeEvent");
  try {
    const activities = await LiveActivity.listAllActivities();
    if (!activities.length) return;
    activities.forEach(async (act: { id: string }) => {
      if (act.id) await LiveActivity.endActivity(act.id);
    });
  } catch (error) {
    console.error(error);
    return;
  }
};

const startLiveActivities = async (
  setActivityTokenId: React.Dispatch<React.SetStateAction<string | null>>
) => {
  if (!(Platform.OS === "ios")) return;
  try {
    await clearAllActivities();
    myModuleEvt.addListener("SomeEvent", (token) => {
      console.log("token ", token);
      if (token.id) {
        setActivityTokenId(token.id);
      }
    });
  } catch (error) {
    console.error(error);
  }
};

const updateLiveActivity = async (activityTokenId: string | null) => {
  if (!(Platform.OS === "ios")) return;
  try {
    if (!activityTokenId) return;
    await LiveActivity.updateActivity(activityTokenId, "🤩");
  } catch (error) {
    console.error(error);
  }
};

export const LiveActivityService = {
  startLiveActivities,
  updateLiveActivity,
  clearAllActivities,
};
