package Entity;

import javax.persistence.*;

@Entity
@Table(name = "student", schema = "public", catalog = "JavaProject")
public class StudentEntity {
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Id
    @Column(name = "id", nullable = false)
    private int id;

    @Basic
    @Column(name = "id_camera",insertable = false, updatable = false, nullable = false)
    private Integer idCamera;
    @Basic
    @Column(name = "nume")
    private String nume;
    @Basic
    @Column(name = "prenume")
    private String prenume;
    @Basic
    @Column(name = "sex")
    private String sex;
    @Basic
    @Column(name = "nationalitate")
    private String nationalitate;
    @Basic
    @Column(name = "medie")
    private Float medie;

    @ManyToOne
    @JoinColumn(name = "id_camera", referencedColumnName = "id")
    private CameraEntity referencedCamera;

    public CameraEntity getReferencedCamera() {
        return referencedCamera;
    }


    public void setReferencedCamera(CameraEntity referencedCamera) {
        this.referencedCamera = referencedCamera;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getIdCamera() {
        return idCamera;
    }

    public void setIdCamera(int idCamera) {
        this.idCamera = idCamera;
    }

    public String getNume() {
        return nume;
    }

    public void setNume(String nume) {
        this.nume = nume;
    }

    public String getPrenume() {
        return prenume;
    }

    public void setPrenume(String prenume) {
        this.prenume = prenume;
    }

    public String getSex() {
        return sex;
    }

    public void setSex(String sex) {
        this.sex = sex;
    }

    public String getNationalitate() {
        return nationalitate;
    }

    public void setNationalitate(String nationalitate) {
        this.nationalitate = nationalitate;
    }

    public Float getMedie() {
        return medie;
    }

    public void setMedie(Float medie) {
        this.medie = medie;
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (o == null || getClass() != o.getClass()) return false;

        StudentEntity student = (StudentEntity) o;

        if (id != student.id) return false;
        if (idCamera != student.idCamera) return false;
        if (nume != null ? !nume.equals(student.nume) : student.nume != null) return false;
        if (prenume != null ? !prenume.equals(student.prenume) : student.prenume != null) return false;
        if (sex != null ? !sex.equals(student.sex) : student.sex != null) return false;
        if (nationalitate != null ? !nationalitate.equals(student.nationalitate) : student.nationalitate != null)
            return false;
        if (medie != null ? !medie.equals(student.medie) : student.medie != null) return false;

        return true;
    }

    @Override
    public int hashCode() {
        int result = id;
        if (idCamera == null)
        {
            result = 31 * result + 0;
        }
        else
        {
            result = 31 * result + idCamera;
        }
        result = 31 * result + (nume != null ? nume.hashCode() : 0);
        result = 31 * result + (prenume != null ? prenume.hashCode() : 0);
        result = 31 * result + (sex != null ? sex.hashCode() : 0);
        result = 31 * result + (nationalitate != null ? nationalitate.hashCode() : 0);
        result = 31 * result + (medie != null ? medie.hashCode() : 0);
        return result;
    }


    @Override
    public String toString() {
        return "StudentEntity{" +
                "id=" + id +
                ", idCamera=" + idCamera +
                ", nume='" + nume + '\'' +
                ", prenume='" + prenume + '\'' +
                ", sex='" + sex + '\'' +
                ", nationalitate='" + nationalitate + '\'' +
                ", medie=" + medie +
                ", referencedCamera=" + referencedCamera +
                '}';
    }
}
