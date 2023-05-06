package Entity;

import javax.persistence.*;

@Entity
@Table(name = "camera", schema = "public", catalog = "JavaProject")
public class CameraEntity {
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Id
    @Column(name = "id")
    private int id;

    @Basic
    @Column(name = "id_camin",insertable = false, updatable = false, nullable = false)
    private int idCamin;

    @ManyToOne
    @JoinColumn(name = "id_camin", referencedColumnName = "id")
    private CaminEntity referencedCamin;

    @Basic
    @Column(name = "capacitate")
    private int capacitate;

    public CaminEntity getReferencedCamin() {
        return referencedCamin;
    }

    public void setReferencedCamin(CaminEntity referencedCamin) {
        this.referencedCamin = referencedCamin;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getIdCamin() {
        return idCamin;
    }

    public void setIdCamin(int idCamin) {
        this.idCamin = idCamin;
    }

    public int getCapacitate() {
        return capacitate;
    }

    public void setCapacitate(int capacitate) {
        this.capacitate = capacitate;
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (o == null || getClass() != o.getClass()) return false;

        CameraEntity that = (CameraEntity) o;

        if (id != that.id) return false;
        if (idCamin != that.idCamin) return false;
        if (capacitate != that.capacitate) return false;

        return true;
    }

    @Override
    public int hashCode() {
        int result = id;
        result = 31 * result + idCamin;
        result = 31 * result + capacitate;
        return result;
    }
}
