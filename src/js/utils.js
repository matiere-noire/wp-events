export const dateFormat = date => date.toLocaleDateString(undefined, { hour: '2-digit', minute: '2-digit' })
export const timeFormat = time => {
  let hours = time.getHours()
  if (hours < 9) {
    hours = `0${hours}`
  }
  let minutes = time.getMinutes()
  if (minutes < 9) {
    minutes = `0${minutes}`
  }
  return `${hours}:${minutes}`
}
