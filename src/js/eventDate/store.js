const { apiFetch } = wp
const { registerStore, dispatch } = wp.data

const DEFAULT_STATE = {
  dates: []
}

const actions = {
  addDate(date) {
    return {
      type: 'ADD_DATE',
      date
    }
  },
  deleteDate(date) {
    return {
      type: 'DELETE_DATE',
      date
    }
  },
  updateDateId(date) {
    return {
      type: 'UPDATE_DATE_ID',
      date
    }
  },
  fetchFromAPI(path) {
    return {
      type: 'FETCH_FROM_API',
      path
    }
  }
}

const store = registerStore('wpe/event-date', {
  reducer(state = DEFAULT_STATE, action) {
    let { dates } = state

    let newDates = [...dates]

    if (action.date) {
      const updateDateIndex = newDates.findIndex(function(element) {
        return element.id === action.date.id
      })

      switch (action.type) {
        case 'ADD_DATE':
          if (updateDateIndex === -1) {
            newDates.push(action.date)
          } else {
            newDates.splice(updateDateIndex, 1, action.date)
          }
          return {
            ...state,
            dates: newDates
          }

        case 'UPDATE_DATE_ID':
          if (updateDateIndex !== -1) {
            const newDate = { ...action.date, id: action.date.newId }

            newDates.splice(updateDateIndex, 1, newDate)
          }

          return {
            ...state,
            dates: newDates
          }
        case 'DELETE_DATE':
          const dateId = Number(action.date.id)

          newDates.splice(updateDateIndex, 1)

          if (!isNaN(dateId)) {
            apiFetch({
              path: `/wpe/v1/dates/${dateId}`,
              method: 'delete'
            })
          }

          return {
            ...state,
            dates: newDates
          }
      }
    }

    return state
  },

  actions,

  selectors: {
    getDates(state, eventID) {
      const { dates } = state
      return dates
    }
  },
  controls: {
    FETCH_FROM_API(action) {
      return apiFetch({ path: action.path })
    }
  },

  resolvers: {
    *getDates(eventID) {
      const path = `/wpe/v1/dates?event_id=${eventID}`
      const dates = yield actions.fetchFromAPI(path)
      dates.forEach(date => {
        dispatch('wpe/event-date').addDate({
          startDate: new Date(date.wpe_date_start),
          endDate: new Date(date.wpe_date_end),
          status: 'saved',
          place: date.wpe_place_id,
          id: date.wpe_date_id
        })
      })
    }
  }
})

export default store
