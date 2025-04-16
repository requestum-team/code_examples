import PushNotificationIOS from "@react-native-community/push-notification-ios";
import { useEffect, useState } from "react";
import {
  Button,
  SafeAreaView,
  StatusBar,
  StyleProp,
  useColorScheme,
  ViewStyle,
} from "react-native";
import { Colors } from "react-native/Libraries/NewAppScreen";

import { LiveActivityService } from "./services/live-activity";

function App(): JSX.Element {
  const isDarkMode = useColorScheme() === "dark";
  const [activityTokenId, setActivityTokenId] = useState<string | null>(null);

  const backgroundStyle: StyleProp<ViewStyle> = {
    backgroundColor: isDarkMode ? Colors.darker : Colors.lighter,
    justifyContent: "center",
    alignItems: "center",
    flex: 1,
    width: "100%",
  };

  useEffect(() => {
    // Request permissions
    PushNotificationIOS.requestPermissions().then((data) => {
      console.log("Push Notification Permissions:", data);
    });

    // Add an event listener to handle device token registration
    const onRegister = (deviceToken: string) => {
      console.log("Device Token:", deviceToken);
    };

    PushNotificationIOS.addEventListener("register", onRegister);

    // Clean up the event listener
    return () => {
      PushNotificationIOS.removeEventListener("register", onRegister);
    };
  }, []);

  return (
    <SafeAreaView style={backgroundStyle}>
      <StatusBar
        barStyle={isDarkMode ? "light-content" : "dark-content"}
        backgroundColor={backgroundStyle.backgroundColor}
      />
      <Button
        title="Activate live activity"
        onPress={() =>
          LiveActivityService.startLiveActivities(setActivityTokenId)
        }
      />
      <Button
        title="Update live activity"
        onPress={() => LiveActivityService.updateLiveActivity(activityTokenId)}
      />
      <Button
        title="Deactivate live activity"
        onPress={LiveActivityService.clearAllActivities}
      />
    </SafeAreaView>
  );
}

export default App;
