const { createElement, Component } = wp.element
const { PanelBody, PanelRow, IconButton, Modal, Button } = wp.components

/**
 * Expose a date with all her datas in the sidebar
 *
 * Receive props : date object, actions (update and delete), all places available
 */
class DateDetails extends Component {
  constructor(props) {
    super(props)

    this.state = {
      confirmeOpen: false
    }
  }

  render() {
    const { confirmeOpen } = this.state
    const { date, editDate, deleteDate, allPlaces } = this.props
    const place = allPlaces.find(p => p.value === Number(date.place))

    return (
      <PanelBody title={`${date.startDate.toLocaleDateString()} #${date.id}`} initialOpen={false}>
        <PanelRow>
          {`${date.startDate.toLocaleDateString()} : `}
          {`${date.startDate.getHours()}h${date.startDate.getMinutes()} => ${date.endDate.getHours()}h${date.endDate.getMinutes()}`}
        </PanelRow>

        <PanelRow>
          <strong>Où ?</strong> {place && place.label}
        </PanelRow>

        <PanelRow>
          <IconButton isDefault icon="edit" label="Modifier" onClick={() => editDate(date)}>
            Modifier
          </IconButton>
          <IconButton isDefault icon="trash" label="Supprimer" onClick={() => this.setState({ confirmeOpen: true })}>
            Supprimer
          </IconButton>
        </PanelRow>

        {confirmeOpen && (
          <Modal title="Suppression de date" onRequestClose={() => this.setState({ confirmeOpen: false })}>
            <p>Etes vous sur de vouloir supprimer la date du {date.startDate.toLocaleDateString()}</p>
            <Button isDefault onClick={() => this.setState({ confirmeOpen: false })}>
              Annuler
            </Button>
            <Button
              isPrimary
              onClick={() => {
                this.setState({ confirmeOpen: false })
                deleteDate(date)
              }}
            >
              Confirmer la suppression
            </Button>
          </Modal>
        )}
      </PanelBody>
    )
  }
}
export default DateDetails
