package Entity;

import javax.persistence.*;

@Entity
@Table(name = "camin", schema = "public", catalog = "JavaProject")
public class CaminEntity {
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Id
    @Column(name = "id")
    private int id;

    @Basic
    @Column(name = "nume")
    private String nume;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getNume() {
        return nume;
    }

    public void setNume(String nume) {
        this.nume = nume;
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (o == null || getClass() != o.getClass()) return false;

        CaminEntity that = (CaminEntity) o;

        if (id != that.id) return false;
        if (nume != null ? !nume.equals(that.nume) : that.nume != null) return false;

        return true;
    }

    @Override
    public int hashCode() {
        int result = id;
        result = 31 * result + (nume != null ? nume.hashCode() : 0);
        return result;
    }
}
