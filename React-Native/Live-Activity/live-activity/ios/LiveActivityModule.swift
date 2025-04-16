import ActivityKit

import Foundation


@objc(LiveActivitiModule)

class LiveActivitiModule: RCTEventEmitter {
  
  // Property to hold the live activity instance
  private var liveActivity: Activity<Live_ActivityAttributes>?
  
  // Boolean to track if listeners are active
  private var hasListeners: Bool = false
  
  // Add other necessary properties
  
  // Starts observing for events, sets up the live activity
  override func startObserving() {
    
    hasListeners = true
    
    do {
      
      // Define the attributes and initial content state of the live activity
      let attributes = Live_ActivityAttributes(name: "Hello")
      let contentState = Live_ActivityAttributes.ContentState(emoji: "😎")
      
      // Request to start a new live activity
      liveActivity = try Activity.request(attributes: attributes, contentState: contentState, pushType: .token)
      
      // Send the initial event to JavaScript
      sendEvent()
      
    } catch (let error) {
      
      print("Error requesting Y Live Activity \(error.localizedDescription).")
      
    }
    
  }
  
  // Stops observing for events
  override func stopObserving() {
    hasListeners = false
  }
  
  // Defines the events that can be sent to JavaScript
  open override func supportedEvents() -> [String] {
    
    ["onReady", "onPending", "onFailure", "SomeEvent"] // etc.
    
  }
  
  // Specifies that the module requires the main thread
  @objc override public static func requiresMainQueueSetup() -> Bool {
    return true
  }
  
  
  // Sends events to JavaScript if there are listeners
  func sendEvent() {
    
    if hasListeners {
      
      Task {
        
        do {
          
          guard let liveActivity = liveActivity else {
            
            // Handle the case where liveActivity is nil, or exit the task.
            
            throw NSError(domain: "Error liveActivity is nil" , code: 555)
            
          }
          
          for try await data in liveActivity.pushTokenUpdates {
            
            let myToken = data.map { String(format: "%02x", $0) }.joined()
            
            print(myToken)
            
            self.sendEvent(withName: "SomeEvent", body: ["token": myToken, "id": liveActivity.id])
            
          }
          
        } catch {
          
          print("Error obtaining token: \(error.localizedDescription)")
          
        }
        
      }
      
    }
    
  }
  
}



// Another class to manage specific live activity actions
@objc(LiveActivity)
class LiveActivity: NSObject {
  
  // Specifies that the module requires the main thread
  @objc static func requiresMainQueueSetup() -> Bool {
    return true
  }
  
  // Ends a specific live activity given its ID
  @objc(endActivity:withResolver:withRejecter:)
  func endActivity(id: String, resolve:RCTPromiseResolveBlock,reject:RCTPromiseRejectBlock) -> Void {
    if #available(iOS 16.1, *) {
      Task {
        await Activity<Live_ActivityAttributes>.activities.filter {$0.id == id}.first?.end(dismissalPolicy: .immediate)
      }
    } else {
      reject("Not available","", NSError())
    }
  }
  
  // Lists all currently active live activities
  @objc(listAllActivities:withRejecter:)
  func listAllActivities(resolve:RCTPromiseResolveBlock,reject:RCTPromiseRejectBlock) -> Void {
    if #available(iOS 16.1, *) {
      var activities = Activity<Live_ActivityAttributes>.activities
      activities.sort { $0.id > $1.id }
      
      return resolve(activities.map{["id": $0.id ]})
    } else {
      reject("Not available", "", NSError())
    }
  }
  
  // Updates a specific live activity given its ID and new emoji state
  @objc(updateActivity:emoji:withResolver:withRejecter:)
  func updateActivity(id: String, emoji: String, resolve:RCTPromiseResolveBlock,reject:RCTPromiseRejectBlock) -> Void {
    if #available(iOS 16.1, *) {
      Task {
        do {
          let updatedStatus = try Live_ActivityAttributes
            .ContentState(emoji: emoji)
          let activities = Activity<Live_ActivityAttributes>.activities
          let activity = activities.filter {$0.id == id}.first
          await activity?.update(using: updatedStatus)
        }catch {
          print("Error: \(error.localizedDescription)")
        }
      }
    } else {
      reject("Not available", "", NSError())
    }
  }
}



