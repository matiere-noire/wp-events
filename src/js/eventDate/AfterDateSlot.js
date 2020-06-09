const { createSlotFill } = wp.components
const { Fill, Slot } = createSlotFill('AfterWPEvent')

const AfterWPEvent = ({ children, classname }) => <Fill>{children}</Fill>

AfterWPEvent.Slot = Slot

export default AfterWPEvent
